<template>
  <section id="contact" class="py-24 px-4 md:px-8 relative bg-[#05070f]">
    <div class="max-w-5xl mx-auto">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Initiate Connection</span>
        <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Contact Telemetry Info -->
        <div>
          <h2 class="text-4xl sm:text-5xl font-black mb-6">
            Let's Build Something <span class="text-gradient-aurora">Exceptional</span>
          </h2>
          <p class="text-slate-300 text-sm leading-relaxed mb-8">
            Available for Full Stack Web & Laravel Filament Development, government profiling systems, and enterprise
            web applications.
          </p>

          <div class="space-y-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-[#00f0ff]/10 text-[#00f0ff] flex items-center justify-center">
                <Mail class="w-6 h-6" />
              </div>
              <div>
                <span class="text-xs font-mono text-slate-400">Direct Email</span>
                <p class="text-white font-bold text-base">cedricjamesleala128@gmail.com</p>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-[#f59e0b]/10 text-[#f59e0b] flex items-center justify-center">
                <Phone class="w-6 h-6" />
              </div>
              <div>
                <span class="text-xs font-mono text-slate-400">Contact Number</span>
                <p class="text-white font-bold text-base">0938-854-2688</p>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-[#8b5cf6]/10 text-[#8b5cf6] flex items-center justify-center">
                <MapPin class="w-6 h-6" />
              </div>
              <div>
                <span class="text-xs font-mono text-slate-400">Location</span>
                <p class="text-white font-bold text-base">Sitio Gulod Muzon, Taytay, Rizal 1920</p>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-[#10b981]/10 text-[#10b981] flex items-center justify-center">
                <Github class="w-6 h-6" />
              </div>
              <div>
                <span class="text-xs font-mono text-slate-400">GitHub Profile</span>
                <a href="https://www.github.com/cdrc1280" target="_blank"
                  class="text-white font-bold text-base hover:text-[#00f0ff] transition-colors block">
                  github.com/cdrc1280
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Glassmorphic Form -->
        <div class="glass-panel p-8 rounded-3xl border border-white/10 relative">
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div>
              <label class="text-xs font-mono text-slate-300 mb-1 block">Full Name</label>
              <input v-model="form.name" type="text" required placeholder="e.g. Hiring Manager"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00f0ff] transition-all" />
            </div>

            <div>
              <label class="text-xs font-mono text-slate-300 mb-1 block">Email Address</label>
              <input v-model="form.email" type="email" required placeholder="recruiter@company.com"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00f0ff] transition-all" />
            </div>

            <div>
              <label class="text-xs font-mono text-slate-300 mb-1 block">Subject</label>
              <input v-model="form.subject" type="text" placeholder="Full Stack Laravel & Filament Project Inquiry"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00f0ff] transition-all" />
            </div>

            <div>
              <label class="text-xs font-mono text-slate-300 mb-1 block">Message</label>
              <textarea v-model="form.message" rows="4" required
                placeholder="Describe project requirements or job opportunity..."
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00f0ff] transition-all"></textarea>
            </div>

            <button type="submit" :disabled="isSubmitting"
              class="w-full py-4 rounded-xl bg-gradient-to-r from-[#00f0ff] via-[#8b5cf6] to-[#ec4899] text-white font-bold text-sm shadow-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
              <Send v-if="!isSubmitting" class="w-4 h-4" />
              <span>{{ isSubmitting ? 'Transmitting Message...' : 'Send Message' }}</span>
            </button>

            <!-- Success Alert Notification -->
            <div v-if="successMsg"
              class="p-4 rounded-xl bg-[#10b981]/20 border border-[#10b981]/40 text-[#10b981] text-xs font-mono animate-fade-in text-center">
              {{ successMsg }}
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { Mail, Phone, MapPin, Github, Send } from 'lucide-vue-next';

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: '',
});

const isSubmitting = ref(false);
const successMsg = ref('');

const handleSubmit = async () => {
  isSubmitting.value = true;
  successMsg.value = '';
  try {
    const response = await axios.post('/api/v1/contact', form.value);
    successMsg.value = response.data.message;
    form.value = { name: '', email: '', subject: '', message: '' };
  } catch (error) {
    alert('Failed to send contact message. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
};
</script>
