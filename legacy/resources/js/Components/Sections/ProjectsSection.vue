<template>
  <section id="projects" class="py-24 px-4 md:px-8 relative">
    <div class="max-w-7xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Portfolio Showcase</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
        <div>
          <h2 class="text-4xl sm:text-5xl font-black">
            Featured <span class="text-gradient-aurora">Architecture & Systems</span>
          </h2>
          <p class="text-slate-300 text-sm mt-2">Production-grade enterprise engines, 3D WebGL portals, and RAG AI agents.</p>
        </div>

        <!-- Search & Filter Controls -->
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative">
            <Search class="w-4 h-4 text-slate-400 absolute left-3 top.1/2 -translate-y-1/2" />
            <input 
              v-model="portfolioStore.searchQuery" 
              type="text" 
              placeholder="Search projects or stack..." 
              class="glass-panel pl-9 pr-4 py-2 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none border border-white/10 focus:border-[#00f0ff] w-48 sm:w-64"
            />
          </div>

          <div class="flex gap-2 glass-panel p-1 rounded-xl border border-white/10 overflow-x-auto max-w-full">
            <button 
              v-for="cat in projectCategories" 
              :key="cat.slug"
              :class="['px-3 py-1.5 rounded-lg text-xs font-mono transition-all shrink-0', portfolioStore.selectedCategory === cat.slug ? 'bg-[#00f0ff] text-slate-950 font-bold' : 'text-slate-400 hover:text-white']"
              @click="portfolioStore.setSelectedCategory(cat.slug)"
            >
              {{ cat.name }}
            </button>
          </div>
        </div>
      </div>

      <!-- Projects Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="project in portfolioStore.filteredProjects" 
          :key="project.id"
          class="glass-panel glass-panel-hover rounded-3xl overflow-hidden border border-white/10 flex flex-col group cursor-pointer"
          @click="portfolioStore.openProjectModal(project)"
        >
          <!-- Project Image Header -->
          <div class="relative h-56 overflow-hidden bg-slate-900">
            <img 
              :src="project.hero_image || 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1200'" 
              :alt="project.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-[#05070f] via-transparent to-transparent"></div>
            
            <span class="absolute top-4 right-4 text-[10px] font-mono px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-[#00f0ff] border border-white/10 flex items-center gap-1">
              <Star class="w-3 h-3 fill-[#00f0ff]" />
              {{ project.stars_count }} Stars
            </span>
          </div>

          <!-- Project Card Body -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <span class="text-xs font-mono text-[#8b5cf6] font-semibold">{{ project.category?.name || 'Fullstack Architecture' }}</span>
              <h3 class="text-xl font-bold text-white mt-1 group-hover:text-[#00f0ff] transition-colors">
                {{ project.title }}
              </h3>
              <p class="text-slate-300 text-xs mt-2 line-clamp-3 leading-relaxed">
                {{ project.summary }}
              </p>
            </div>

            <!-- Tech Stack Tags & Actions -->
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between">
              <div class="flex flex-wrap gap-1.5 max-w-[75%]">
                <span 
                  v-for="t in project.tech_stack.slice(0, 3)" 
                  :key="t"
                  class="text-[10px] font-mono px-2 py-0.5 rounded bg-white/5 text-slate-300 border border-white/5"
                >
                  {{ t }}
                </span>
                <span v-if="project.tech_stack.length > 3" class="text-[10px] font-mono text-slate-400">
                  +{{ project.tech_stack.length - 3 }}
                </span>
              </div>

              <div class="flex items-center gap-2">
                <ExternalLink class="w-4 h-4 text-slate-400 group-hover:text-[#00f0ff] transition-colors" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Project Modal Detail -->
      <Teleport to="body">
        <div 
          v-if="portfolioStore.selectedProject" 
          class="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
          @click.self="portfolioStore.closeProjectModal()"
        >
          <div class="glass-panel w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-3xl border border-white/20 p-6 md:p-8 relative animate-scale-up">
            <button class="absolute top-6 right-6 p-2 text-slate-400 hover:text-white" @click="portfolioStore.closeProjectModal()">
              <X class="w-6 h-6" />
            </button>

            <span class="text-xs font-mono text-[#00f0ff] uppercase">{{ portfolioStore.selectedProject.category?.name }}</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-1 mb-4">
              {{ portfolioStore.selectedProject.title }}
            </h2>

            <img 
              :src="portfolioStore.selectedProject.hero_image" 
              class="w-full h-72 object-cover rounded-2xl mb-6 border border-white/10" 
            />

            <!-- Architecture Breakdown -->
            <div class="space-y-6 text-sm text-slate-200">
              <div>
                <h4 class="text-base font-bold text-white mb-2 flex items-center gap-2">
                  <Cpu class="w-4 h-4 text-[#8b5cf6]" /> System Architecture
                </h4>
                <p class="leading-relaxed text-slate-300">{{ portfolioStore.selectedProject.architecture }}</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                  <h5 class="font-bold text-[#ec4899] mb-1">Key Challenges</h5>
                  <p class="text-xs text-slate-300 leading-relaxed">{{ portfolioStore.selectedProject.challenges }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                  <h5 class="font-bold text-[#10b981] mb-1">Engineered Solution</h5>
                  <p class="text-xs text-slate-300 leading-relaxed">{{ portfolioStore.selectedProject.solutions }}</p>
                </div>
              </div>
            </div>

            <!-- External Links -->
            <div class="mt-8 pt-6 border-t border-white/10 flex flex-wrap gap-4">
              <a 
                v-if="portfolioStore.selectedProject.demo_url" 
                :href="portfolioStore.selectedProject.demo_url" 
                target="_blank"
                class="px-6 py-3 rounded-xl bg-[#00f0ff] text-slate-950 font-bold text-xs flex items-center gap-2 hover:opacity-90 transition-opacity"
              >
                Live Demo <ExternalLink class="w-4 h-4" />
              </a>
              <a 
                v-if="portfolioStore.selectedProject.github_url" 
                :href="portfolioStore.selectedProject.github_url" 
                target="_blank"
                class="px-6 py-3 rounded-xl glass-panel text-white font-bold text-xs flex items-center gap-2 hover:border-[#00f0ff] transition-all"
              >
                GitHub Repository <Github class="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { Search, Star, ExternalLink, X, Cpu, Github } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();

const projectCategories = computed(() => [
  { name: 'All', slug: 'all' },
  ...portfolioStore.projectCategories,
]);
</script>
