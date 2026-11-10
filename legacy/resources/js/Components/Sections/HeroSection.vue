<template>
    <section id="hero" class="relative min-h-screen flex items-center justify-center pt-24 pb-16 px-4 md:px-8">
        <div class="max-w-5xl w-full mx-auto text-center z-10 flex flex-col items-center">

            <!-- Profile Image Container with Glow Aura -->
            <div class="relative mb-6 group cursor-pointer">
                <div
                    class="absolute -inset-1.5 bg-linear-to-r from-[#00f0ff] via-[#8b5cf6] to-[#ec4899] rounded-full blur-md opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse">
                </div>
                <img :src="profileImg" alt="Cedric James Leala"
                    class="relative w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover border-2 border-white/20 shadow-2xl group-hover:scale-105 transition-transform duration-500" />
                <div class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-[#10b981] border-2 border-[#05070f] shadow-lg"
                    title="Available for Full Stack Development"></div>
            </div>

            <!-- Availability Badge -->
            <div
                class="inline-flex items-center gap-2 glass-panel px-4 py-2 rounded-full border border-white/10 mb-6 animate-fade-in">
                <span class="w-2.5 h-2.5 rounded-full bg-[#10b981] animate-ping"></span>
                <span class="text-xs font-mono text-slate-300">
                    {{ availabilityStatus }}
                </span>
            </div>

            <!-- Main Headline Typography -->
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-black tracking-tight leading-[1.08] mb-6">
                Full Stack Developer
                <span class="block text-gradient-aurora mt-2">AI-Assisted Development (Claude Code)</span>
            </h1>

            <!-- Professional Subtitle -->
            <p class="text-base sm:text-xl text-slate-300 max-w-3xl font-light mb-10 leading-relaxed">
                {{ heroSubtitle }}
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-4 mb-16">
                <a href="#projects"
                    class="px-8 py-4 rounded-2xl bg-linear-to-r from-[#00f0ff] via-[#8b5cf6] to-[#ec4899] text-white font-bold text-base shadow-[0_0_30px_rgba(0,240,255,0.4)] hover:scale-105 transition-all flex items-center gap-2">
                    Explore Projects
                    <ArrowRight class="w-5 h-5" />
                </a>

                <button @click="aiStore.openDrawer()"
                    class="glass-panel px-8 py-4 rounded-2xl border border-white/20 text-white font-bold text-base hover:border-[#00f0ff] hover:text-[#00f0ff] transition-all flex items-center gap-2">
                    <Bot class="w-5 h-5 text-[#ec4899]" />
                    Ask AetherAI
                </button>

                <a href="/api/v1/resume/download" target="_blank"
                    class="glass-panel px-6 py-4 rounded-2xl border border-white/10 text-slate-300 hover:text-white transition-all flex items-center gap-2">
                    <Download class="w-5 h-5" />
                    Resume
                </a>
            </div>

            <!-- Realtime Statistics Ticker -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full max-w-4xl">
                <div class="glass-panel p-6 rounded-2xl text-center border border-white/10">
                    <div class="text-3xl sm:text-4xl font-extrabold text-[#00f0ff] font-mono">
                        {{ portfolioStore.settings.years_of_experience || '3' }}
                    </div>
                    <div class="text-xs text-slate-400 font-mono uppercase mt-1">Years Exp.</div>
                </div>
                <div class="glass-panel p-6 rounded-2xl text-center border border-white/10">
                    <div class="text-3xl sm:text-4xl font-extrabold text-[#8b5cf6] font-mono">
                        {{ portfolioStore.settings.production_systems || '6' }}
                    </div>
                    <div class="text-xs text-slate-400 font-mono uppercase mt-1">Projects</div>
                </div>
                <div class="glass-panel p-6 rounded-2xl text-center border border-white/10">
                    <div class="text-3xl sm:text-4xl font-extrabold text-[#ec4899] font-mono">100%</div>
                    <div class="text-xs text-slate-400 font-mono uppercase mt-1">AI-Assisted</div>
                </div>
                <div class="glass-panel p-6 rounded-2xl text-center border border-white/10">
                    <div class="text-3xl sm:text-4xl font-extrabold text-[#10b981] font-mono">
                        {{ portfolioStore.settings.system_uptime || '100%' }}
                    </div>
                    <div class="text-xs text-slate-400 font-mono uppercase mt-1">Uptime</div>
                </div>
            </div>

        </div>
    </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { useAiStore } from '@/Stores/useAiStore';
import { ArrowRight, Bot, Download } from 'lucide-vue-next';

const portfolioStore = usePortfolioStore();
const aiStore = useAiStore();

const profileImg = computed(() => {
    return portfolioStore.settings.profile_image || '/images/cedric-profile.jpg';
});

const availabilityStatus = computed(() => {
    return portfolioStore.settings.availability_status || 'Available for Full Stack & AI-Assisted Development';
});

const heroSubtitle = computed(() => {
    return portfolioStore.settings.hero_subtitle || 'Full Stack Developer with three years of experience shipping and maintaining six concurrently running production systems for TESDA. Daily practitioner of AI-assisted workflows using Claude Code, ChatGPT & Gemini to build production-ready software fast.';
});
</script>
