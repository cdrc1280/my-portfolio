<template>
  <section id="testimonials" class="py-24 px-4 md:px-8 relative bg-[#05070f]/80">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Endorsements</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <h2 class="text-4xl sm:text-5xl font-black mb-12">
        Client & Executive <span class="text-gradient-aurora">Testimonials</span>
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div 
          v-for="item in portfolioStore.testimonials" 
          :key="item.id"
          class="glass-panel glass-panel-hover p-8 rounded-3xl border border-white/10 flex flex-col justify-between"
        >
          <div class="mb-6">
            <!-- 5 Star Rating -->
            <div class="flex items-center gap-1 mb-4">
              <Star v-for="s in item.rating" :key="s" class="w-4 h-4 fill-[#f59e0b] text-[#f59e0b]" />
            </div>

            <p class="text-slate-200 text-sm leading-relaxed italic">
              "{{ item.content }}"
            </p>
          </div>

          <div class="flex items-center gap-4 pt-4 border-t border-white/10">
            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#00f0ff] to-[#8b5cf6] flex items-center justify-center font-bold text-slate-950 font-mono text-xs">
              {{ item.client_name.charAt(0) }}
            </div>
            <div>
              <h4 class="text-white font-bold text-sm">{{ item.client_name }}</h4>
              <p class="text-xs text-slate-400 font-mono">{{ item.position }} @ {{ item.company }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { Star } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();
</script>
