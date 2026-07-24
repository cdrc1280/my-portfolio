<template>
  <section id="certificates" class="py-24 px-4 md:px-8 relative bg-[#05070f]/70">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Accreditations</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <h2 class="text-4xl sm:text-5xl font-black mb-12">
        Verified <span class="text-gradient-aurora">Certificates</span>
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div 
          v-for="cert in portfolioStore.certificates" 
          :key="cert.id"
          class="glass-panel p-6 rounded-2xl border border-white/10 flex items-center justify-between hover:border-[#00f0ff]/40 transition-all"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-[#00f0ff]/20 to-[#8b5cf6]/20 border border-[#00f0ff]/30 flex items-center justify-center shrink-0">
              <Award class="w-6 h-6 text-[#00f0ff]" />
            </div>
            <div>
              <h4 class="text-lg font-bold text-white">{{ cert.title }}</h4>
              <p class="text-xs text-slate-400 font-mono">{{ cert.issuer }} • {{ cert.issue_date }}</p>
            </div>
          </div>

          <a 
            v-if="cert.credential_url" 
            :href="cert.credential_url" 
            target="_blank"
            class="p-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-[#00f0ff] text-slate-300 hover:text-[#00f0ff] transition-all"
            title="Verify Credential"
          >
            <ExternalLink class="w-4 h-4" />
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { Award, ExternalLink } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();
</script>
