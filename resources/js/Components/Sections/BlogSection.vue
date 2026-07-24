<template>
  <section id="blog" class="py-24 px-4 md:px-8 relative">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Thought Leadership</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <h2 class="text-4xl sm:text-5xl font-black mb-12">
        Technical <span class="text-gradient-aurora">Articles & Research</span>
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div 
          v-for="blog in portfolioStore.blogs" 
          :key="blog.id"
          class="glass-panel glass-panel-hover rounded-3xl overflow-hidden border border-white/10 flex flex-col cursor-pointer"
          @click="portfolioStore.openBlogModal(blog)"
        >
          <img 
            :src="blog.cover_image || 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1200'" 
            class="w-full h-48 object-cover" 
          />
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-3 text-xs font-mono text-slate-400 mb-2">
                <span class="text-[#00f0ff]">{{ blog.category?.name || 'Engineering' }}</span>
                <span>•</span>
                <span>{{ blog.reading_time }} min read</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">{{ blog.title }}</h3>
              <p class="text-slate-300 text-xs leading-relaxed line-clamp-2">{{ blog.summary }}</p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between text-xs font-mono text-[#00f0ff]">
              Read Article <ArrowRight class="w-4 h-4" />
            </div>
          </div>
        </div>
      </div>

      <!-- Blog Modal -->
      <Teleport to="body">
        <div 
          v-if="portfolioStore.selectedBlog" 
          class="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
          @click.self="portfolioStore.closeBlogModal()"
        >
          <div class="glass-panel w-full max-w-3xl max-h-[85vh] overflow-y-auto rounded-3xl border border-white/20 p-6 md:p-8 relative">
            <button class="absolute top-6 right-6 p-2 text-slate-400 hover:text-white" @click="portfolioStore.closeBlogModal()">
              <X class="w-6 h-6" />
            </button>

            <span class="text-xs font-mono text-[#00f0ff] uppercase">{{ portfolioStore.selectedBlog.category?.name }}</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-1 mb-4">
              {{ portfolioStore.selectedBlog.title }}
            </h2>

            <div class="prose prose-invert max-w-none text-slate-300 text-sm leading-relaxed" v-html="renderMarkdown(portfolioStore.selectedBlog.content)"></div>
          </div>
        </div>
      </Teleport>
    </div>
  </section>
</template>

<script setup lang="ts">
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { ArrowRight, X } from 'lucide-vue-next';
import { marked } from 'marked';

const portfolioStore = usePortfolioStore();

const renderMarkdown = (text: string) => {
  return marked.parse(text);
};
</script>
