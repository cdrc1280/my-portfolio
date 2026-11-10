<template>
  <div>
    <!-- Floating AI Trigger Orb Button -->
    <button 
      class="fixed bottom-6 right-6 z-[990] glass-panel px-4 py-3 rounded-full flex items-center gap-3 border border-[#00f0ff]/50 shadow-[0_0_25px_rgba(0,240,255,0.3)] hover:scale-105 transition-all group"
      @click="aiStore.toggleDrawer"
    >
      <div class="relative w-8 h-8 rounded-full bg-gradient-to-tr from-[#00f0ff] to-[#8b5cf6] flex items-center justify-center animate-pulse">
        <Bot class="w-5 h-5 text-white" />
        <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-[#ec4899] border-2 border-[#05070f]"></span>
      </div>
      <span class="text-white font-bold text-sm hidden sm:inline group-hover:text-[#00f0ff] transition-colors">
        AetherAI
      </span>
    </button>

    <!-- Slide-Out Glass AI Drawer -->
    <Teleport to="body">
      <div 
        v-if="aiStore.isDrawerOpen" 
        class="fixed inset-0 z-[9995] bg-black/60 backdrop-blur-sm flex justify-end"
        @click.self="aiStore.closeDrawer"
      >
        <div class="glass-panel w-full max-w-md h-full flex flex-col border-l border-white/10 shadow-2xl animate-slide-left">
          <!-- Drawer Header -->
          <div class="p-4 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#00f0ff] to-[#8b5cf6] flex items-center justify-center">
                <Bot class="w-5 h-5 text-white" />
              </div>
              <div>
                <h3 class="text-white font-bold text-base flex items-center gap-2">
                  AetherAI Assistant
                  <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#00f0ff]/20 text-[#00f0ff] font-mono">RAG v2.0</span>
                </h3>
                <p class="text-xs text-slate-400">Indexed Knowledge Base</p>
              </div>
            </div>
            <button class="p-2 text-slate-400 hover:text-white" @click="aiStore.closeDrawer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Messages Body -->
          <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
            <div 
              v-for="msg in aiStore.messages" 
              :key="msg.id" 
              :class="['flex gap-3', msg.role === 'user' ? 'flex-row-reverse' : 'flex-row']"
            >
              <!-- Avatar -->
              <div :class="['w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold', msg.role === 'user' ? 'bg-[#8b5cf6] text-white' : 'bg-[#00f0ff]/20 text-[#00f0ff]']">
                {{ msg.role === 'user' ? 'You' : 'AI' }}
              </div>

              <!-- Message Content Bubble -->
              <div :class="['max-w-[82%] p-3.5 rounded-2xl text-sm leading-relaxed', msg.role === 'user' ? 'bg-[#8b5cf6]/20 text-white border border-[#8b5cf6]/40 rounded-tr-none' : 'glass-panel text-slate-200 rounded-tl-none border border-white/10']">
                <div v-html="renderMarkdown(msg.content)"></div>

                <!-- Suggested Actions -->
                <div v-if="msg.suggested_actions && msg.suggested_actions.length > 0" class="mt-3 pt-3 border-t border-white/10 flex flex-wrap gap-2">
                  <button 
                    v-for="(act, idx) in msg.suggested_actions" 
                    :key="idx"
                    class="text-xs px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 hover:border-[#00f0ff] hover:text-[#00f0ff] transition-all text-slate-300 flex items-center gap-1"
                    @click="executeAction(act)"
                  >
                    <Sparkles class="w-3 h-3 text-[#00f0ff]" />
                    {{ act.label }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Loading Dots -->
            <div v-if="aiStore.isSending" class="flex items-center gap-2 text-slate-400 text-xs glass-panel px-4 py-2.5 rounded-2xl w-fit">
              <div class="w-2 h-2 rounded-full bg-[#00f0ff] animate-ping"></div>
              Querying RAG Vector Index...
            </div>
          </div>

          <!-- Drawer Footer Input -->
          <div class="p-4 border-t border-white/10 bg-[#05070f]/50">
            <div class="flex items-center gap-2 bg-white/5 p-2 rounded-2xl border border-white/10 focus-within:border-[#00f0ff] transition-all">
              <input 
                v-model="inputQuery" 
                type="text" 
                placeholder="Ask about Cedric's experience, stack, or projects..." 
                class="flex-1 bg-transparent text-white text-sm focus:outline-none px-2"
                @keyup.enter="handleSend"
              />
              
              <!-- Voice Button -->
              <button 
                class="p-2 rounded-xl text-slate-400 hover:text-[#00f0ff] transition-colors"
                :class="{ 'text-[#ec4899] animate-pulse': aiStore.isListening }"
                @click="aiStore.toggleVoiceInput"
                title="Voice Input"
              >
                <Mic class="w-4 h-4" />
              </button>

              <!-- Send Button -->
              <button 
                class="p-2.5 rounded-xl bg-gradient-to-r from-[#00f0ff] to-[#8b5cf6] text-white hover:opacity-90 transition-opacity"
                @click="handleSend"
              >
                <Send class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { useAiStore } from '@/Stores/useAiStore';
import { usePortfolioStore } from '@/Stores/usePortfolioStore';
import { Bot, X, Send, Mic, Sparkles } from 'lucide-vue-next';
import { marked } from 'marked';

const aiStore = useAiStore();
const portfolioStore = usePortfolioStore();
const inputQuery = ref('');
const chatContainer = ref<HTMLDivElement | null>(null);

const renderMarkdown = (text: string) => {
  return marked.parse(text);
};

const handleSend = () => {
  if (!inputQuery.value.trim()) return;
  const q = inputQuery.value;
  inputQuery.value = '';
  aiStore.sendMessage(q);
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
};

const executeAction = (act: any) => {
  if (act.action === 'scroll_to' && act.target) {
    aiStore.closeDrawer();
    const el = document.querySelector(act.target);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  } else if (act.action === 'filter_projects' && act.category) {
    aiStore.closeDrawer();
    portfolioStore.setSelectedCategory(act.category);
    const el = document.querySelector('#projects');
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  } else if (act.action === 'download_resume') {
    window.open('/api/v1/resume/download', '_blank');
  } else if (act.action === 'ask_ai' && act.target) {
    aiStore.sendMessage(act.target);
  }
};
</script>
