<template>
  <section id="experience" class="py-24 px-4 md:px-8 relative bg-[#05070f]/60">
    <div class="max-w-5xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Career Timeline</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <h2 class="text-4xl sm:text-5xl font-black mb-16">
        Professional <span class="text-gradient-aurora">Track Record</span>
      </h2>

      <!-- Vertical Timeline -->
      <div class="relative border-l-2 border-white/10 pl-6 md:pl-10 space-y-12 ml-4">
        <div 
          v-for="exp in portfolioStore.experiences" 
          :key="exp.id"
          class="relative group"
        >
          <!-- Timeline Pulsing Dot -->
          <div class="absolute -left-[31px] md:-left-[47px] top-1.5 w-5 h-5 rounded-full bg-[#05070f] border-2 border-[#00f0ff] flex items-center justify-center group-hover:scale-125 transition-transform">
            <div class="w-2 h-2 rounded-full bg-[#00f0ff] animate-ping"></div>
          </div>

          <!-- Glass Card -->
          <div class="glass-panel glass-panel-hover p-6 md:p-8 rounded-3xl border border-white/10">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-2">
              <div>
                <h3 class="text-2xl font-bold text-white">{{ exp.position }}</h3>
                <h4 class="text-base text-[#00f0ff] font-mono font-semibold">{{ exp.company }}</h4>
              </div>
              <span class="text-xs font-mono px-3 py-1 rounded-full bg-white/10 text-slate-300 w-fit">
                {{ exp.start_date }} - {{ exp.is_current ? 'Present' : exp.end_date }}
              </span>
            </div>

            <p class="text-slate-300 text-sm mb-4 leading-relaxed">{{ exp.summary }}</p>

            <!-- Responsibilities -->
            <ul class="space-y-2 mb-6">
              <li v-for="(resp, idx) in exp.responsibilities" :key="idx" class="text-xs text-slate-300 flex items-start gap-2">
                <span class="text-[#00f0ff] font-bold">▹</span>
                <span>{{ resp }}</span>
              </li>
            </ul>

            <!-- Tech Tags -->
            <div class="flex flex-wrap gap-2 pt-4 border-t border-white/10">
              <span 
                v-for="s in exp.skills_used" 
                :key="s"
                class="text-[10px] font-mono px-2.5 py-1 rounded-lg bg-white/5 text-[#8b5cf6] border border-[#8b5cf6]/20"
              >
                {{ s }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { usePortfolioStore } from '@/Stores/usePortfolioStore';

const portfolioStore = usePortfolioStore();
</script>
