import { defineStore } from 'pinia';

export type ThemePreset = 'aurora' | 'cyberpunk' | 'midnight' | 'matrix' | 'light';

export const useThemeStore = defineStore('theme', {
  state: () => ({
    currentTheme: 'aurora' as ThemePreset,
    isSoundMuted: true,
    isParticlesEnabled: true,
    isCommandPaletteOpen: false,
    primaryColor: '#00f0ff',
    secondaryColor: '#8b5cf6',
  }),

  actions: {
    setTheme(theme: ThemePreset) {
      this.currentTheme = theme;
      if (theme === 'cyberpunk') {
        this.primaryColor = '#ff0055';
        this.secondaryColor = '#ffe600';
      } else if (theme === 'midnight') {
        this.primaryColor = '#38bdf8';
        this.secondaryColor = '#6366f1';
      } else if (theme === 'matrix') {
        this.primaryColor = '#22c55e';
        this.secondaryColor = '#10b981';
      } else if (theme === 'light') {
        this.primaryColor = '#0284c7';
        this.secondaryColor = '#7c3aed';
      } else {
        // Aurora
        this.primaryColor = '#00f0ff';
        this.secondaryColor = '#8b5cf6';
      }
      document.documentElement.setAttribute('data-theme', theme);
    },

    toggleLightMode() {
      if (this.currentTheme === 'light') {
        this.setTheme('aurora');
      } else {
        this.setTheme('light');
      }
    },

    toggleSound() {
      this.isSoundMuted = !this.isSoundMuted;
    },

    toggleParticles() {
      this.isParticlesEnabled = !this.isParticlesEnabled;
    },

    toggleCommandPalette() {
      this.isCommandPaletteOpen = !this.isCommandPaletteOpen;
    }
  }
});
