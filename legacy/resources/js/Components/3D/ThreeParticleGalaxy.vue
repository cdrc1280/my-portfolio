<template>
  <div ref="canvasContainer" class="fixed inset-0 pointer-events-none z-[-1]"></div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import * as THREE from 'three';

const canvasContainer = ref<HTMLDivElement | null>(null);
let scene: THREE.Scene;
let camera: THREE.PerspectiveCamera;
let renderer: THREE.WebGLRenderer;
let particlesMesh: THREE.Points;

let mouseX = 0;
let mouseY = 0;
let animationFrameId: number;

const handleMouseMove = (event: MouseEvent) => {
  mouseX = (event.clientX - window.innerWidth / 2) * 0.0005;
  mouseY = (event.clientY - window.innerHeight / 2) * 0.0005;
};

const initThree = () => {
  if (!canvasContainer.value) return;

  scene = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
  camera.position.z = 5;

  renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  canvasContainer.value.appendChild(renderer.domElement);

  // Particle Geometry
  const particleCount = 1200;
  const geometry = new THREE.BufferGeometry();
  const positions = new Float32Array(particleCount * 3);
  const colors = new Float32Array(particleCount * 3);

  const color1 = new THREE.Color('#00f0ff');
  const color2 = new THREE.Color('#8b5cf6');
  const color3 = new THREE.Color('#ec4899');

  for (let i = 0; i < particleCount * 3; i += 3) {
    positions[i] = (Math.random() - 0.5) * 15;
    positions[i + 1] = (Math.random() - 0.5) * 15;
    positions[i + 2] = (Math.random() - 0.5) * 15;

    const mixedColor = color1.clone().lerp(color2, Math.random()).lerp(color3, Math.random());
    colors[i] = mixedColor.r;
    colors[i + 1] = mixedColor.g;
    colors[i + 2] = mixedColor.b;
  }

  geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

  // Particle Material
  const material = new THREE.PointsMaterial({
    size: 0.035,
    vertexColors: true,
    transparent: true,
    opacity: 0.8,
    blending: THREE.AdditiveBlending,
  });

  particlesMesh = new THREE.Points(geometry, material);
  scene.add(particlesMesh);

  window.addEventListener('mousemove', handleMouseMove);
  window.addEventListener('resize', onWindowResize);

  animate();
};

const animate = () => {
  animationFrameId = requestAnimationFrame(animate);

  if (particlesMesh) {
    particlesMesh.rotation.y += 0.001;
    particlesMesh.rotation.x += 0.0005;

    particlesMesh.rotation.y += (mouseX - particlesMesh.rotation.y) * 0.05;
    particlesMesh.rotation.x += (mouseY - particlesMesh.rotation.x) * 0.05;
  }

  renderer.render(scene, camera);
};

const onWindowResize = () => {
  if (!camera || !renderer) return;
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
};

onMounted(() => {
  initThree();
});

onUnmounted(() => {
  cancelAnimationFrame(animationFrameId);
  window.removeEventListener('mousemove', handleMouseMove);
  window.removeEventListener('resize', onWindowResize);
  if (renderer && renderer.domElement) {
    renderer.domElement.remove();
  }
});
</script>
