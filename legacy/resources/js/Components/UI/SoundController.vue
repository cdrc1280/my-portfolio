<template>
  <div class="fixed bottom-6 left-6 z-[990] flex items-center gap-2">
    <!-- Futuristic Ambient Audio & UI Sound Effects Controller -->
    <button 
      class="glass-panel p-3 rounded-full border border-white/10 text-slate-300 hover:text-[#00f0ff] hover:border-[#00f0ff]/40 transition-all flex items-center gap-2 text-xs font-mono shadow-xl bg-black/40 backdrop-blur-md"
      :title="isPlaying ? 'Mute Futuristic Ambient Sound' : 'Enable Futuristic Ambient Sound'"
      @click="toggleAmbient"
    >
      <component :is="isPlaying ? Volume2 : VolumeX" :class="['w-4 h-4', isPlaying ? 'text-[#00f0ff] animate-pulse' : 'text-slate-500']" />
      <span class="hidden sm:inline">{{ isPlaying ? 'Audio Active' : 'Audio Muted' }}</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Volume2, VolumeX } from 'lucide-vue-next';

const isPlaying = ref(false);
let audioCtx: AudioContext | null = null;
let osc1: OscillatorNode | null = null;
let osc2: OscillatorNode | null = null;
let masterGain: GainNode | null = null;

const toggleAmbient = async () => {
  if (!isPlaying.value) {
    try {
      const AudioCtxClass = window.AudioContext || (window as any).webkitAudioContext;
      audioCtx = new AudioCtxClass();

      if (audioCtx.state === 'suspended') {
        await audioCtx.resume();
      }

      masterGain = audioCtx.createGain();
      masterGain.gain.setValueAtTime(0.04, audioCtx.currentTime); // Soft ambient volume

      // Create dual harmonic sine wave synth (C3 130.81Hz + G3 196.00Hz perfect fifth)
      osc1 = audioCtx.createOscillator();
      osc2 = audioCtx.createOscillator();

      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(130.81, audioCtx.currentTime);

      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(196.00, audioCtx.currentTime);

      osc1.connect(masterGain);
      osc2.connect(masterGain);
      masterGain.connect(audioCtx.destination);

      osc1.start();
      osc2.start();

      isPlaying.value = true;
    } catch (e) {
      console.warn('Web Audio API not supported or blocked by browser policies.', e);
    }
  } else {
    stopSound();
  }
};

const stopSound = () => {
  if (osc1) {
    try { osc1.stop(); osc1.disconnect(); } catch (e) {}
    osc1 = null;
  }
  if (osc2) {
    try { osc2.stop(); osc2.disconnect(); } catch (e) {}
    osc2 = null;
  }
  if (audioCtx) {
    try { audioCtx.close(); } catch (e) {}
    audioCtx = null;
  }
  isPlaying.value = false;
};
</script>
