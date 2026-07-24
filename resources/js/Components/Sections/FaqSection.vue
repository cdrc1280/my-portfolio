<template>
  <section id="faqs" class="py-24 px-4 md:px-8 relative">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Clarifications</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <h2 class="text-4xl sm:text-5xl font-black mb-12">
        Frequently Asked <span class="text-gradient-aurora">Questions</span>
      </h2>

      <div class="space-y-4">
        <div 
          v-for="(faq, idx) in portfolioStore.faqs" 
          :key="faq.id"
          class="glass-panel rounded-2xl border border-white/10 overflow-hidden transition-all"
        >
          <button 
            class="w-full p-6 text-left flex items-center justify-between font-bold text-white text-base hover:text-[#00f0ff] transition-colors"
            @click="toggleFaq(idx)"
          >
            <span>{{ faq.question }}</span>
            <ChevronDown :class="['w-5 h-5 text-[#00f0ff] transition-transform duration-300', activeIdx === idx ? 'rotate-180' : '']" />
          </button>

          <div v-if="activeIdx === idx" class="px-6 pb-6 text-slate-300 text-sm leading-relaxed border-t border-white/10 pt-4 animate-fade-in">
            {{ faq.answer }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { ChevronDown } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();
const activeIdx = ref<number | null>(0);

const toggleFaq = (idx: number) => {
  activeIdx.value = activeIdx.value === idx ? null : idx;
};
</script>
