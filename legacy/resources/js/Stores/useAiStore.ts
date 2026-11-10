import { defineStore } from 'pinia';
import axios from 'axios';

export interface ChatMessage {
  id: string;
  role: 'user' | 'assistant';
  content: string;
  suggested_actions?: Array<{ label: string; action: string; target?: string; category?: string }>;
  timestamp: Date;
}

export const useAiStore = defineStore('ai', {
  state: () => ({
    isDrawerOpen: false,
    isListening: false,
    isSpeaking: false,
    isSending: false,
    sessionId: '',
    messages: [
      {
        id: '1',
        role: 'assistant',
        content: "👋 Greetings! I am **AetherAI**, Cedric James Leala's RAG-powered assistant. How can I help you explore his TESDA production systems, Full Stack Laravel & Filament experience, or AI-assisted workflows?",
        suggested_actions: [
          { label: 'Find Laravel Projects', action: 'filter_projects', category: 'backend-ai' },
          { label: 'Download Cedric\'s Resume', action: 'download_resume' },
          { label: 'What is Cedric\'s strongest skill?', action: 'ask_ai', target: 'What is Cedric\'s strongest skill?' },
        ],
        timestamp: new Date(),
      }
    ] as ChatMessage[],
  }),

  actions: {
    toggleDrawer() {
      this.isDrawerOpen = !this.isDrawerOpen;
    },

    openDrawer() {
      this.isDrawerOpen = true;
    },

    closeDrawer() {
      this.isDrawerOpen = false;
    },

    async sendMessage(userMessage: string) {
      if (!userMessage.trim() || this.isSending) return;

      const userMsgObj: ChatMessage = {
        id: Date.now().toString(),
        role: 'user',
        content: userMessage,
        timestamp: new Date(),
      };

      this.messages.push(userMsgObj);
      this.isSending = true;

      try {
        const response = await axios.post('/api/v1/ai/chat', {
          message: userMessage,
          session_id: this.sessionId || undefined,
        });

        if (response.data.session_id) {
          this.sessionId = response.data.session_id;
        }

        const aiMsgObj: ChatMessage = {
          id: (Date.now() + 1).toString(),
          role: 'assistant',
          content: response.data.reply,
          suggested_actions: response.data.suggested_actions || [],
          timestamp: new Date(),
        };

        this.messages.push(aiMsgObj);

        if (this.isSpeaking && 'speechSynthesis' in window) {
          this.speakText(response.data.reply);
        }
      } catch (error) {
        console.error('Failed to send AI message:', error);
        this.messages.push({
          id: (Date.now() + 1).toString(),
          role: 'assistant',
          content: 'Sorry, I encountered a temporary connection issue. Please feel free to try again!',
          timestamp: new Date(),
        });
      } finally {
        this.isSending = false;
      }
    },

    speakText(text: string) {
      if (!('speechSynthesis' in window)) return;
      window.speechSynthesis.cancel();
      const plainText = text.replace(/[#*`_]/g, '');
      const utterance = new SpeechSynthesisUtterance(plainText);
      utterance.rate = 1.05;
      utterance.pitch = 1.0;
      window.speechSynthesis.speak(utterance);
    },

    toggleVoiceInput() {
      if (!('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) {
        alert('Voice input is not supported in your browser.');
        return;
      }

      const SpeechRecognition = (window as any).SpeechRecognition || (window as any).webkitSpeechRecognition;
      const recognition = new SpeechRecognition();
      recognition.continuous = false;
      recognition.interimResults = false;

      if (!this.isListening) {
        this.isListening = true;
        recognition.start();

        recognition.onresult = (event: any) => {
          const transcript = event.results[0][0].transcript;
          this.sendMessage(transcript);
          this.isListening = false;
        };

        recognition.onerror = () => {
          this.isListening = false;
        };

        recognition.onend = () => {
          this.isListening = false;
        };
      } else {
        this.isListening = false;
        recognition.stop();
      }
    }
  }
});
