<template>
  <section id="services" class="py-24 px-4 md:px-8 relative">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Offerings</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <h2 class="text-4xl sm:text-5xl font-black mb-16">
        Specialized <span class="text-gradient-aurora">Architectural Services</span>
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div 
          v-for="service in portfolioStore.services" 
          :key="service.id"
          class="glass-panel glass-panel-hover p-8 rounded-3xl border border-white/10 flex flex-col justify-between group"
        >
          <div>
            <span class="text-[10px] font-mono px-3 py-1 rounded-full bg-[#00f0ff]/10 text-[#00f0ff] uppercase font-bold">
              {{ service.badge }}
            </span>

            <h3 class="text-2xl font-bold text-white mt-4 mb-3 group-hover:text-[#00f0ff] transition-colors">
              {{ service.title }}
            </h3>

            <p class="text-slate-300 text-xs leading-relaxed mb-6">
              {{ service.short_description }}
            </p>

            <ul class="space-y-2">
              <li v-for="feat in service.features" :key="feat" class="text-xs text-slate-300 flex items-center gap-2">
                <CheckCircle2 class="w-4 h-4 text-[#10b981]" />
                {{ feat }}
              </li>
            </ul>
          </div>

          <a 
            href="#contact" 
            class="mt-8 pt-4 border-t border-white/10 text-xs font-mono font-bold text-[#00f0ff] flex items-center gap-2 group-hover:translate-x-2 transition-transform"
          >
            Inquire Engagement <ArrowRight class="w-4 h-4" />
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { CheckCircle2, ArrowRight } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();
</script>
