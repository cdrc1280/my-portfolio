<template>
  <section id="about" class="py-24 px-4 md:px-8 relative bg-[#05070f]">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Profile & Engineering Philosophy</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <h2 class="text-4xl sm:text-5xl font-black mb-8">
        Full Stack Engineering with <span class="text-gradient-aurora">AI-Assisted Precision</span>
      </h2>

      <!-- Personal Info & Profile Container Box -->
      <div class="glass-panel p-8 md:p-10 rounded-3xl border border-white/10 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#00f0ff]/5 rounded-full filter blur-3xl pointer-events-none"></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          <!-- Profile Photo Showcase Container -->
          <div class="lg:col-span-4 flex flex-col items-center text-center">
            <div class="relative group cursor-pointer mb-4">
              <div class="absolute -inset-1 bg-linear-to-r from-[#00f0ff] via-[#8b5cf6] to-[#ec4899] rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-500"></div>
              <img 
                :src="profileImg" 
                alt="Cedric James Leala" 
                class="relative w-64 h-80 rounded-2xl object-cover border border-white/20 shadow-2xl group-hover:scale-102 transition-transform duration-500"
              />
            </div>
            <h4 class="text-lg font-bold text-white font-mono">Cedric James Leala</h4>
            <span class="text-xs text-[#00f0ff] font-mono">Full Stack & AI Developer</span>
          </div>

          <div class="lg:col-span-8">
            <h3 class="text-2xl font-bold text-white mb-4">Professional Overview</h3>
            <p class="text-slate-300 text-base leading-relaxed mb-6 font-sans">
              {{ professionalSummary }}
            </p>

            <!-- Personal Information Grid -->
            <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 gap-4 pt-6 border-t border-white/10 text-xs font-mono text-slate-300">
              <div><span class="text-slate-500 block">BIRTHDATE</span> Jan 28, 2000</div>
              <div><span class="text-slate-500 block">AGE</span> 26 Years Old</div>
              <div><span class="text-slate-500 block">NATIONALITY</span> Filipino</div>
              <div><span class="text-slate-500 block">CIVIL STATUS</span> Single</div>
              <div><span class="text-slate-500 block">RELIGION</span> Roman Catholic</div>
              <div><span class="text-slate-500 block">LOCATION</span> Taytay, Rizal 1920</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature Highlight Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Card 1 -->
        <div class="glass-panel glass-panel-hover p-8 rounded-3xl border border-white/10 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-[#00f0ff]/10 text-[#00f0ff] flex items-center justify-center mb-6">
              <Bot class="w-6 h-6" />
            </div>
            <h3 class="text-2xl font-bold mb-4">Claude Code & AI Workflows</h3>
            <p class="text-slate-300 text-sm leading-relaxed">
              Hands-on daily use of Claude Code CLI for scaffolding, rapid debugging, and iterating on production code, alongside ChatGPT and Gemini for code reviews.
            </p>
          </div>
          <div class="mt-8 pt-4 border-t border-white/10 text-xs font-mono text-[#00f0ff]">
            Fast Build-Test-Ship Workflow
          </div>
        </div>

        <!-- Card 2 -->
        <div class="glass-panel glass-panel-hover p-8 rounded-3xl border border-white/10 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-[#8b5cf6]/10 text-[#8b5cf6] flex items-center justify-center mb-6">
              <Server class="w-6 h-6" />
            </div>
            <h3 class="text-2xl font-bold mb-4">Laravel & Filament Core</h3>
            <p class="text-slate-300 text-sm leading-relaxed">
              End-to-end web engineering covering database schemas, REST APIs, Filament v4 admin panel controls, and query optimization for high-traffic systems.
            </p>
          </div>
          <div class="mt-8 pt-4 border-t border-white/10 text-xs font-mono text-[#8b5cf6]">
            End-to-End Ownership
          </div>
        </div>

        <!-- Card 3 -->
        <div class="glass-panel glass-panel-hover p-8 rounded-3xl border border-white/10 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-[#ec4899]/10 text-[#ec4899] flex items-center justify-center mb-6">
              <Code2 class="w-6 h-6" />
            </div>
            <h3 class="text-2xl font-bold mb-4">Vue.js, React & Inertia.js</h3>
            <p class="text-slate-300 text-sm leading-relaxed">
              Modern frontend web development with Vue 3, React.js, TypeScript, Tailwind CSS, and Inertia.js bridge integration.
            </p>
          </div>
          <div class="mt-8 pt-4 border-t border-white/10 text-xs font-mono text-[#ec4899]">
            Seamless UI/UX Integration
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { Bot, Server, Code2 } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();

const profileImg = computed(() => {
  return portfolioStore.settings.profile_image || '/images/cedric-profile.jpg';
});

const professionalSummary = computed(() => {
  return portfolioStore.settings.professional_summary || 
    'Full Stack Developer with three years of experience shipping and maintaining six concurrently running production systems for TESDA, owning each end to end schema, API, and UI. Core stack is Laravel, Filament, Vue.js, and MySQL, with additional front-end experience in React, TypeScript, and Inertia.js from concurrent freelance work. Uses Claude Code and other AI coding assistants as part of a daily build-test-ship workflow to move fast without sacrificing maintainability, and is comfortable taking a scoped problem from requirements to working, production-ready software independently.';
});
</script>
