"""Cut the person out of the photo and estimate a depth map, cropped to the person."""
import sys, numpy as np, onnxruntime as ort
from PIL import Image, ImageFilter
from rembg import remove, new_session

src, model_path, out = sys.argv[1], sys.argv[2], sys.argv[3]
img = Image.open(src).convert('RGB')
W, H = img.size

# 1. Person matte
session = new_session(sys.argv[4] if len(sys.argv) > 4 else 'u2net_human_seg')
rgba = remove(img, session=session)
alpha = np.array(rgba)[:, :, 3]

# De-fringe: replace the colour of semi-transparent edge pixels with colour bled outward from the
# confident interior, so sky blue/white doesn't glow around the silhouette on a dark scene.
rgb = np.asarray(img, dtype=np.float32)
core = (alpha > 235).astype(np.float32)
num = rgb * core[..., None]
den = core.copy()
def blur(a, r):
    out = a
    for _ in range(3):  # 3 box passes ~ gaussian
        k = 2 * r + 1
        c = np.cumsum(np.pad(out, [(r + 1, r)] + [(0, 0)] * (out.ndim - 1), mode='edge'), axis=0)
        out = (c[k:] - c[:-k]) / k
        c = np.cumsum(np.pad(out, [(0, 0), (r + 1, r)] + [(0, 0)] * (out.ndim - 2), mode='edge'), axis=1)
        out = (c[:, k:] - c[:, :-k]) / k
    return out
bled = blur(num, 6) / np.maximum(blur(den, 6)[..., None], 1e-4)
edge = (alpha < 235)[..., None]
fixed = np.where(edge, bled, rgb).clip(0, 255).astype(np.uint8)
alpha = np.where(alpha < 40, 0, alpha).astype(np.uint8)  # drop faint halo
rgba = Image.fromarray(np.dstack([fixed, alpha]), 'RGBA')

# 2. Depth (Depth Anything V2 small, ONNX). Input 518x518, ImageNet normalisation; output = relative inverse depth.
sess = ort.InferenceSession(model_path, providers=['CPUExecutionProvider'])
x = np.asarray(img.resize((518, 518), Image.BICUBIC), dtype=np.float32) / 255.0
x = (x - [0.485, 0.456, 0.406]) / [0.229, 0.224, 0.225]
x = x.transpose(2, 0, 1)[None].astype(np.float32)
depth = sess.run(None, {sess.get_inputs()[0].name: x})[0].squeeze()
depth = np.array(Image.fromarray(depth.astype(np.float32)).resize((W, H), Image.BICUBIC))

# Normalise depth over the person only (near = white), then soften to avoid spiky geometry.
mask = alpha > 128
d = depth.copy()
lo, hi = np.percentile(d[mask], 2), np.percentile(d[mask], 99.5)
d = np.clip((d - lo) / (hi - lo), 0, 1)
# Extend the person's depth past the outline (instead of 0) so edge triangles don't stretch backwards.
m = mask.astype(np.float32)
spread = blur(d * m, 10) / np.maximum(blur(m, 10), 1e-4)
d = np.where(mask, d, spread * np.clip(blur(m, 10) * 4, 0, 1))
depth_img = Image.fromarray((d * 255).astype(np.uint8)).filter(ImageFilter.GaussianBlur(2))

# 3. Crop to the person with a small margin, keep aspect, and save.
ys, xs = np.where(alpha > 20)
pad = 24
box = (max(0, xs.min() - pad), max(0, ys.min() - pad), min(W, xs.max() + pad), min(H, ys.max() + 1))
rgba.crop(box).save(f'{out}/cedric-cutout.png')
rgba.crop(box).save(f'{out}/climber.webp', quality=88, method=6)
depth_img.crop(box).save(f'{out}/cedric-depth.png')
depth_img.crop(box).convert('RGB').save(f'{out}/climber-depth.webp', quality=90, method=6)
print('crop box', box, 'size', (box[2] - box[0], box[3] - box[1]))
