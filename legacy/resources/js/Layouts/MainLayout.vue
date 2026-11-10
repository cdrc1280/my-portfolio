<template>
  <div class="relative min-h-screen text-slate-100 selection:bg-[#00f0ff] selection:text-slate-950">
    <!-- Aurora Background Blobs & Noise -->
    <div class="aurora-bg">
      <div class="aurora-blob aurora-blob-1"></div>
      <div class="aurora-blob aurora-blob-2"></div>
      <div class="aurora-blob aurora-blob-3"></div>
    </div>
    <div class="noise-overlay"></div>

    <!-- 3D Particle Galaxy Background -->
    <ThreeParticleGalaxy />

    <!-- Custom Cursor -->
    <CustomCursor />

    <!-- Command Palette Spotlight -->
    <CommandPalette />

    <!-- Ambient Sound Toggle -->
    <SoundController />

    <!-- AI Assistant Drawer -->
    <AiAssistantDrawer />

    <!-- Top Futuristic Glass Navbar -->
    <header class="fixed top-0 left-0 right-0 z-[900] px-3 sm:px-6 md:px-8 py-3 sm:py-4">
      <nav class="max-w-7xl mx-auto glass-panel px-4 sm:px-6 py-3 rounded-2xl flex items-center justify-between border border-white/10 shadow-lg">
        <a href="#hero" class="flex items-center gap-2 font-black text-lg sm:text-xl tracking-tight text-white group">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#00f0ff] to-[#8b5cf6] flex items-center justify-center font-mono text-sm text-slate-950 font-bold group-hover:scale-110 transition-transform">
            CJL
          </div>
          <span class="truncate max-w-[140px] xs:max-w-none">Cedric <span class="hidden xs:inline">James</span> <span class="text-[#00f0ff]">Leala</span></span>
        </a>

        <!-- Desktop Navigation Links -->
        <div class="hidden lg:flex items-center gap-6 xl:gap-8 text-xs font-mono font-medium text-slate-300">
          <a href="#about" class="hover:text-[#00f0ff] transition-colors">About</a>
          <a href="#skills" class="hover:text-[#00f0ff] transition-colors">3D Skills</a>
          <a href="#projects" class="hover:text-[#00f0ff] transition-colors">Projects</a>
          <a href="#experience" class="hover:text-[#00f0ff] transition-colors">Experience</a>
          <a href="#services" class="hover:text-[#00f0ff] transition-colors">Services</a>
          <a href="#blog" class="hover:text-[#00f0ff] transition-colors">Blog</a>
          <a href="#contact" class="hover:text-[#00f0ff] transition-colors">Contact</a>
        </div>

        <!-- Right Quick Tools -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Light / Dark Mode Toggle Button -->
          <button 
            @click="themeStore.toggleLightMode()" 
            class="p-2 rounded-xl glass-panel border border-white/10 text-slate-300 hover:text-[#00f0ff] hover:border-[#00f0ff]/40 transition-all flex items-center gap-1.5 text-xs font-mono"
            :title="themeStore.currentTheme === 'light' ? 'Switch to Dark Mode' : 'Switch to Light Mode'"
          >
            <Sun v-if="themeStore.currentTheme === 'light'" class="w-4 h-4 text-amber-500" />
            <Moon v-else class="w-4 h-4 text-[#00f0ff]" />
            <span class="hidden sm:inline">{{ themeStore.currentTheme === 'light' ? 'Light' : 'Dark' }}</span>
          </button>

          <!-- Search / Cmd+K Spotlight -->
          <button 
            @click="themeStore.toggleCommandPalette()" 
            class="hidden sm:flex items-center gap-2 glass-panel px-3 py-1.5 rounded-xl border border-white/10 text-xs font-mono text-slate-400 hover:text-white transition-colors"
          >
            <Search class="w-3.5 h-3.5 text-[#00f0ff]" />
            <span>Search</span>
            <kbd class="text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-slate-300">⌘K</kbd>
          </button>

          <!-- Mobile Hamburger Menu Button -->
          <button 
            @click="isMobileMenuOpen = !isMobileMenuOpen" 
            class="lg:hidden p-2 rounded-xl glass-panel border border-white/10 text-slate-300 hover:text-white transition-colors"
            aria-label="Toggle Mobile Menu"
          >
            <Menu v-if="!isMobileMenuOpen" class="w-5 h-5" />
            <X v-else class="w-5 h-5 text-[#00f0ff]" />
          </button>
        </div>
      </nav>

      <!-- Mobile Navigation Drawer Overlay -->
      <transition 
        enter-active-class="transition duration-300 ease-out" 
        enter-from-class="opacity-0 -translate-y-4" 
        enter-to-class="opacity-100 translate-y-0" 
        leave-active-class="transition duration-200 ease-in" 
        leave-from-class="opacity-100 translate-y-0" 
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div 
          v-if="isMobileMenuOpen" 
          class="lg:hidden max-w-7xl mx-auto mt-2 glass-panel p-6 rounded-2xl border border-white/10 shadow-2xl flex flex-col gap-4 text-sm font-mono"
        >
          <a @click="isMobileMenuOpen = false" href="#about" class="py-2 border-b border-white/5 hover:text-[#00f0ff]">About Cedric</a>
          <a @click="isMobileMenuOpen = false" href="#skills" class="py-2 border-b border-white/5 hover:text-[#00f0ff]">3D Skill Galaxy</a>
          <a @click="isMobileMenuOpen = false" href="#projects" class="py-2 border-b border-white/5 hover:text-[#00f0ff]">Projects Showcase</a>
          <a @click="isMobileMenuOpen = false" href="#experience" class="py-2 border-b border-white/5 hover:text-[#00f0ff]">Career Experience</a>
          <a @click="isMobileMenuOpen = false" href="#services" class="py-2 border-b border-white/5 hover:text-[#00f0ff]">Services & Stack</a>
          <a @click="isMobileMenuOpen = false" href="#blog" class="py-2 border-b border-white/5 hover:text-[#00f0ff]">Tech Blog</a>
          <a @click="isMobileMenuOpen = false" href="#contact" class="py-2 hover:text-[#00f0ff]">Contact</a>

          <button 
            @click="isMobileMenuOpen = false; themeStore.toggleCommandPalette()" 
            class="w-full flex items-center justify-center gap-2 glass-panel py-3 rounded-xl border border-white/10 text-xs text-slate-300 hover:text-white mt-2"
          >
            <Search class="w-4 h-4 text-[#00f0ff]" />
            <span>Search Portfolio (⌘K)</span>
          </button>
        </div>
      </transition>
    </header>

    <!-- Main Page Content Slot -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="py-12 border-t border-white/10 relative z-10 text-center text-xs font-mono text-slate-400 px-4">
      <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
          © 2026 Cedric James Leala. Built with Laravel 12, Filament v4, Vue 3 & Three.js.
        </div>

        <div class="flex items-center gap-6">
          <a href="https://www.github.com/cdrc1280" target="_blank" class="hover:text-[#00f0ff]">GitHub</a>
          <a href="mailto:cedricjamesleala128@gmail.com" class="hover:text-[#00f0ff]">Email</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Lenis from 'lenis';
import { useThemeStore } from '@/Stores/useThemeStore';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import ThreeParticleGalaxy from '@/Components/3D/ThreeParticleGalaxy.vue';
import CustomCursor from '@/Components/UI/CustomCursor.vue';
import CommandPalette from '@/Components/UI/CommandPalette.vue';
import AiAssistantDrawer from '@/Components/UI/AiAssistantDrawer.vue';
import SoundController from '@/Components/UI/SoundController.vue';
import { Search, Sun, Moon, Menu, X } from 'lucide-vue-next';

const themeStore = useThemeStore();
const portfolioStore = usePortfolioStore();
const isMobileMenuOpen = ref(false);

onMounted(() => {
  // Lenis Smooth Scroll Initialization
  const lenis = new Lenis({
    duration: 1.2,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  });

  function raf(time: number) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  }

  requestAnimationFrame(raf);

  // Fetch initial portfolio & GitHub stats
  portfolioStore.fetchPortfolioData();
  portfolioStore.fetchGithubStats();
});
</script>
