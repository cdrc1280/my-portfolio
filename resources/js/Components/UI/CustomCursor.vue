<template>
  <div class="hidden md:block pointer-events-none fixed inset-0 z-[9999]">
    <!-- Primary Glowing Dot -->
    <div 
      class="fixed w-3 h-3 bg-[#00f0ff] rounded-full -translate-x-1/2 -translate-y-1/2 shadow-[0_0_15px_#00f0ff] transition-transform duration-75"
      :style="{ left: `${dotX}px`, top: `${dotY}px` }"
    ></div>

    <!-- Smooth Follower Ring -->
    <div 
      class="fixed w-9 h-9 border border-[#00f0ff]/50 rounded-full -translate-x-1/2 -translate-y-1/2 transition-all duration-300 ease-out"
      :style="{ left: `${ringX}px`, top: `${ringY}px` }"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

const dotX = ref(0);
const dotY = ref(0);
const ringX = ref(0);
const ringY = ref(0);

const onMouseMove = (e: MouseEvent) => {
  dotX.value = e.clientX;
  dotY.value = e.clientY;

  setTimeout(() => {
    ringX.value = e.clientX;
    ringY.value = e.clientY;
  }, 50);
};

onMounted(() => {
  window.addEventListener('mousemove', onMouseMove);
});

onUnmounted(() => {
  window.removeEventListener('mousemove', onMouseMove);
});
</script>
