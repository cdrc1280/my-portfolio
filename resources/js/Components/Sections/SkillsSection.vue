<template>
  <section id="skills" class="py-24 px-4 md:px-8 relative bg-[#05070f]/80">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Technical Galaxy</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
          <h2 class="text-4xl sm:text-5xl font-black">
            3D Interactive <span class="text-gradient-aurora">Skill Sphere</span>
          </h2>
          <p class="text-slate-300 text-sm mt-2">
            Rotate the core or hover over technology planets to inspect telemetry & years of experience.
          </p>
        </div>

        <!-- Category Filter Tabs -->
        <div class="flex flex-wrap gap-2 glass-panel p-1.5 rounded-2xl border border-white/10 w-fit">
          <button 
            v-for="cat in skillCategories" 
            :key="cat.id"
            :class="['px-4 py-2 rounded-xl text-xs font-mono transition-all', activeCat === cat.slug ? 'bg-gradient-to-r from-[#00f0ff] to-[#8b5cf6] text-white font-bold shadow-md' : 'text-slate-400 hover:text-white']"
            @click="activeCat = cat.slug"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>

      <!-- 3D Skill Galaxy Canvas -->
      <div class="glass-panel rounded-3xl border border-white/10 overflow-hidden relative shadow-2xl">
        <ThreeSkillGalaxy />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import ThreeSkillGalaxy from '@/Components/3D/ThreeSkillGalaxy.vue';

const portfolioStore = usePortfolioStore();
const activeCat = ref('all');

const skillCategories = computed(() => [
  { id: 0, name: 'All Stack', slug: 'all' },
  ...portfolioStore.skillCategories,
]);
</script>
