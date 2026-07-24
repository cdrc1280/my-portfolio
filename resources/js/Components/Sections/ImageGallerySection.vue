<template>
  <section id="gallery" class="py-24 px-4 md:px-8 relative bg-[#05070f]">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Visual Showcase & Work Demonstration</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div>
          <h2 class="text-4xl sm:text-5xl font-black">
            System Screenshots & <span class="text-gradient-aurora">Media Showcase</span>
          </h2>
          <p class="text-slate-300 text-sm mt-2">
            Click any image to view high-resolution production system previews & certificate media.
          </p>
        </div>

        <div class="flex gap-2 glass-panel p-1 rounded-xl border border-white/10 overflow-x-auto max-w-full">
          <button 
            v-for="filter in filters" 
            :key="filter.id"
            :class="['px-3 py-1.5 rounded-lg text-xs font-mono transition-all shrink-0', activeFilter === filter.id ? 'bg-[#00f0ff] text-slate-950 font-bold' : 'text-slate-400 hover:text-white']"
            @click="activeFilter = filter.id"
          >
            {{ filter.label }}
          </button>
        </div>
      </div>

      <!-- Image Grid Containers -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div 
          v-for="(item, index) in filteredItems" 
          :key="index"
          class="glass-panel glass-panel-hover rounded-3xl overflow-hidden border border-white/10 group cursor-pointer relative h-64 flex flex-col justify-end p-6"
          @click="openLightbox(item.image, item.title)"
        >
          <img 
            :src="item.image" 
            :alt="item.title"
            class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
          />
          <div class="absolute inset-0 bg-gradient-to-t from-[#05070f] via-[#05070f]/40 to-transparent"></div>

          <div class="relative z-10">
            <span class="text-[10px] font-mono px-2.5 py-1 rounded-full bg-[#00f0ff]/20 text-[#00f0ff] border border-[#00f0ff]/40">
              {{ item.category }}
            </span>
            <h3 class="text-lg font-bold text-white mt-2 group-hover:text-[#00f0ff] transition-colors">
              {{ item.title }}
            </h3>
            <p class="text-xs text-slate-300 line-clamp-1 mt-0.5">{{ item.caption }}</p>
          </div>

          <div class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/60 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
            <Maximize2 class="w-4 h-4 text-[#00f0ff]" />
          </div>
        </div>
      </div>

      <!-- Image Lightbox Modal -->
      <Teleport to="body">
        <div 
          v-if="lightboxImage" 
          class="fixed inset-0 z-[99999] bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
          @click.self="closeLightbox"
        >
          <div class="relative max-w-5xl w-full flex flex-col items-center">
            <button class="absolute -top-12 right-0 text-slate-400 hover:text-white p-2" @click="closeLightbox">
              <X class="w-8 h-8" />
            </button>
            <img :src="lightboxImage" class="max-h-[80vh] w-auto object-contain rounded-2xl border border-white/20 shadow-2xl" />
            <h4 class="text-white font-bold text-lg mt-4 font-mono">{{ lightboxTitle }}</h4>
          </div>
        </div>
      </Teleport>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { Maximize2, X } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();
const activeFilter = ref('all');
const lightboxImage = ref<string | null>(null);
const lightboxTitle = ref('');

const filters = [
  { id: 'all', label: 'All Media' },
  { id: 'systems', label: 'Production Systems' },
  { id: 'certs', label: 'Certificates' },
];

const mediaItems = computed(() => {
  const items: Array<{ title: string; image: string; category: string; caption: string; filterId: string }> = [];

  portfolioStore.projects.forEach(p => {
    if (p.hero_image) {
      items.push({
        title: p.title,
        image: p.hero_image,
        category: 'TESDA Production System',
        caption: p.summary,
        filterId: 'systems',
      });
    }
  });

  portfolioStore.certificates.forEach(c => {
    items.push({
      title: c.title,
      image: c.image || 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=800',
      category: 'Certification / Training',
      caption: `${c.issuer} (${c.issue_date})`,
      filterId: 'certs',
    });
  });

  return items;
});

const filteredItems = computed(() => {
  if (activeFilter.value === 'all') return mediaItems.value;
  return mediaItems.value.filter(i => i.filterId === activeFilter.value);
});

const openLightbox = (img: string, title: string) => {
  lightboxImage.value = img;
  lightboxTitle.value = title;
};

const closeLightbox = () => {
  lightboxImage.value = null;
};
</script>
