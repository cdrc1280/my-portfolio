<template>
  <Teleport to="body">
    <div 
      v-if="themeStore.isCommandPaletteOpen" 
      class="fixed inset-0 z-[9999] bg-black/70 backdrop-blur-md flex items-start justify-center pt-24 px-4 animate-fade-in"
      @click.self="themeStore.toggleCommandPalette"
    >
      <div class="glass-panel w-full max-w-2xl rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
        <!-- Search Input -->
        <div class="flex items-center px-4 py-3 border-b border-white/10 gap-3">
          <Search class="w-5 h-5 text-[#00f0ff]" />
          <input 
            v-model="query" 
            ref="inputRef" 
            type="text" 
            placeholder="Type a command or search sections, projects, themes..." 
            class="w-full bg-transparent text-white focus:outline-none placeholder-slate-400 text-sm font-medium"
            @keydown.esc="themeStore.toggleCommandPalette"
          />
          <kbd class="text-xs px-2 py-0.5 rounded bg-white/10 text-slate-400 font-mono">ESC</kbd>
        </div>

        <!-- Command List -->
        <div class="max-h-80 overflow-y-auto p-2">
          <!-- Section Navigation -->
          <div class="text-xs uppercase font-mono text-slate-400 px-3 py-1.5">Navigation</div>
          <button 
            v-for="item in navCommands" 
            :key="item.target" 
            class="w-full text-left px-3 py-2 rounded-xl text-sm flex items-center justify-between text-slate-200 hover:bg-white/10 hover:text-[#00f0ff] transition-all"
            @click="navigateTo(item.target)"
          >
            <span class="flex items-center gap-2">
              <component :is="item.icon" class="w-4 h-4" />
              {{ item.label }}
            </span>
            <span class="text-xs font-mono text-slate-400">{{ item.shortcut }}</span>
          </button>

          <!-- Theme Presets -->
          <div class="text-xs uppercase font-mono text-slate-400 px-3 py-1.5 mt-2">Themes</div>
          <button 
            v-for="t in themes" 
            :key="t.id" 
            class="w-full text-left px-3 py-2 rounded-xl text-sm flex items-center justify-between text-slate-200 hover:bg-white/10 hover:text-[#00f0ff] transition-all"
            @click="selectTheme(t.id as any)"
          >
            <span class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: t.color }"></span>
              {{ t.name }}
            </span>
            <span v-if="themeStore.currentTheme === t.id" class="text-xs text-[#00f0ff] font-mono">Active</span>
          </button>

          <!-- Quick Actions -->
          <div class="text-xs uppercase font-mono text-slate-400 px-3 py-1.5 mt-2">Actions</div>
          <button 
            class="w-full text-left px-3 py-2 rounded-xl text-sm flex items-center gap-2 text-slate-200 hover:bg-white/10 hover:text-[#00f0ff] transition-all"
            @click="openAi"
          >
            <Bot class="w-4 h-4 text-[#ec4899]" />
            Ask RAG AI Assistant
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { useThemeStore, type ThemePreset } from '@/Stores/useThemeStore';
import { useAiStore } from '@/Stores/useAiStore';
import { Search, Home, User, Cpu, Layers, Briefcase, Mail, Bot } from 'lucide-vue-next';

const themeStore = useThemeStore();
const aiStore = useAiStore();
const query = ref('');
const inputRef = ref<HTMLInputElement | null>(null);

const navCommands = [
  { label: 'Jump to Hero', target: '#hero', icon: Home, shortcut: 'Shift+H' },
  { label: 'Jump to About', target: '#about', icon: User, shortcut: 'Shift+A' },
  { label: '3D Skill Galaxy', target: '#skills', icon: Cpu, shortcut: 'Shift+S' },
  { label: 'Projects Showcase', target: '#projects', icon: Layers, shortcut: 'Shift+P' },
  { label: 'Experience Timeline', target: '#experience', icon: Briefcase, shortcut: 'Shift+E' },
  { label: 'Contact Cedric', target: '#contact', icon: Mail, shortcut: 'Shift+C' },
];

const themes = [
  { id: 'aurora', name: 'Aurora Glass (Default Dark)', color: '#00f0ff' },
  { id: 'light', name: 'Light Mode Daylight', color: '#0284c7' },
  { id: 'cyberpunk', name: 'Cyberpunk Neon', color: '#ff0055' },
  { id: 'midnight', name: 'Midnight Deep', color: '#38bdf8' },
  { id: 'matrix', name: 'Matrix Digital', color: '#22c55e' },
];

const navigateTo = (target: string) => {
  themeStore.isCommandPaletteOpen = false;
  const el = document.querySelector(target);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' });
  }
};

const selectTheme = (themeId: ThemePreset) => {
  themeStore.setTheme(themeId);
  themeStore.isCommandPaletteOpen = false;
};

const openAi = () => {
  themeStore.isCommandPaletteOpen = false;
  aiStore.openDrawer();
};

const handleKeyDown = (e: KeyboardEvent) => {
  if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault();
    themeStore.toggleCommandPalette();
    if (themeStore.isCommandPaletteOpen) {
      nextTick(() => inputRef.value?.focus());
    }
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});
</script>
