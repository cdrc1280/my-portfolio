<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Github, GitCommit } from 'lucide-vue-next';
import axios from 'axios';

const githubData = ref<any>(null);
const loading = ref(true);
const loadFailed = ref(false);

const fetchStats = async () => {
    try {
        const { data } = await axios.get('/api/v1/github/stats');
        githubData.value = data;
    } catch (e) {
        loadFailed.value = true;
    } finally {
        loading.value = false;
    }
};

onMounted(fetchStats);

const username = computed(() => githubData.value?.username ?? 'cdrc1280');

const contributionsAvailable = computed(() => !!githubData.value?.contributions?.available);
const totalContributions = computed(() => githubData.value?.contributions?.total);
const contributionWeeks = computed(() => githubData.value?.contributions?.weeks ?? []);

// GitHub's calendar weeks always start on Sunday, so row index 1 = Monday,
// row index 3 = Wednesday, row index 5 = Friday — matches GitHub's own label spacing.
const dayLabelRows: Record<number, string> = { 1: 'Mon', 3: 'Wed', 5: 'Fri' };

// Build month labels aligned to the week column where that month first appears,
// the same way GitHub's own graph does it.
const monthLabels = computed(() => {
    const weeks = contributionWeeks.value;
    if (!weeks.length) return [];

    const labels: { weekIndex: number; label: string }[] = [];
    let lastMonth: number | null = null;

    weeks.forEach((week: any, weekIndex: number) => {
        const firstDay = week.contributionDays?.[0]?.date;
        if (!firstDay) return;

        const date = new Date(firstDay);
        const month = date.getMonth();

        if (month !== lastMonth) {
            labels.push({
                weekIndex,
                label: date.toLocaleString('en-US', { month: 'short' }),
            });
            lastMonth = month;
        }
    });

    return labels;
});

const latestCommits = computed(() => githubData.value?.latest_commits ?? []);

const reposAvailable = computed(() => !!githubData.value?.repos?.available);
const totalStars = computed(() => githubData.value?.repos?.total_stars);
const totalForks = computed(() => githubData.value?.repos?.total_forks);
const topLanguages = computed(() => githubData.value?.repos?.top_languages ?? []);

const getHeatmapColor = (count: number) => {
    if (count === 0) return 'bg-white/5';
    if (count <= 2) return 'bg-[#00f0ff]/25';
    if (count <= 5) return 'bg-[#00f0ff]/50';
    if (count <= 9) return 'bg-[#00f0ff]/75';
    return 'bg-[#00f0ff]';
};
</script>

<template>
    <section id="github-stats" class="py-20 px-4 md:px-8 relative bg-[#05070f]">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-[#00f0ff] font-mono text-sm uppercase tracking-wider">// Code Metrics &
                    Activity</span>
                <div class="h-px bg-[#00f0ff]/30 flex-1"></div>
            </div>

            <h2 class="text-3xl sm:text-4xl font-black mb-10">
                Live <span class="text-gradient-aurora">GitHub Telemetry</span>
            </h2>

            <div v-if="loading" class="text-slate-400 font-mono text-sm">Loading live stats…</div>
            <div v-else-if="loadFailed" class="text-red-400 font-mono text-sm">
                Couldn't reach the stats endpoint right now.
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Contribution graph + recent commits -->
                <div
                    class="md:col-span-2 glass-panel p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <Github class="w-5 h-5 text-[#00f0ff]" />
                                <a :href="`https://github.com/${username}`" target="_blank"
                                    class="font-bold text-white text-base hover:text-[#00f0ff] transition-colors font-mono">
                                    @{{ username }}
                                </a>
                            </div>
                            <span v-if="contributionsAvailable"
                                class="text-xs font-mono px-3 py-1 rounded-full bg-[#10b981]/20 text-[#10b981]">
                                {{ totalContributions.toLocaleString() }} Contributions (Past Year)
                            </span>
                        </div>

                        <div v-if="contributionsAvailable" class="overflow-x-auto">
                            <div class="inline-flex flex-col gap-1 min-w-full">
                                <!-- Month labels row -->
                                <div class="grid gap-1 text-[10px] font-mono text-slate-500 pl-8"
                                    :style="{ gridTemplateColumns: `repeat(${contributionWeeks.length}, 12px)` }">
                                    <span v-for="(week, wi) in contributionWeeks" :key="`m-${wi}`" class="relative">
                                        <span v-if="monthLabels.find(m => m.weekIndex === wi)"
                                            class="absolute -top-1 left-0">
                                            {{monthLabels.find(m => m.weekIndex === wi)?.label}}
                                        </span>
                                    </span>
                                </div>

                                <!-- Weekday labels + heatmap grid -->
                                <div class="flex gap-1">
                                    <div class="grid grid-rows-7 gap-1 text-[10px] font-mono text-slate-500 w-8 pt-0.5">
                                        <span v-for="row in 7" :key="`d-${row}`" class="h-3 flex items-center">
                                            {{ dayLabelRows[row - 1] || '' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-flow-col grid-rows-7 gap-1">
                                        <template v-for="(week, wi) in contributionWeeks" :key="wi">
                                            <div v-for="(day, di) in week.contributionDays" :key="di"
                                                class="w-3 h-3 rounded-sm transition-transform hover:scale-125"
                                                :class="getHeatmapColor(day.contributionCount)"
                                                :title="`${day.date}: ${day.contributionCount} contributions`"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-xs font-mono text-slate-500 py-4">
                            Contribution graph unavailable — requires GITHUB_TOKEN configured server-side.
                        </div>

                        <div v-if="contributionsAvailable"
                            class="flex items-center justify-end gap-2 text-xs font-mono text-slate-400 mt-4">
                            <span>Less</span>
                            <div class="flex items-center gap-1">
                                <div class="w-2.5 h-2.5 rounded-sm bg-white/5"></div>
                                <div class="w-2.5 h-2.5 rounded-sm bg-[#00f0ff]/25"></div>
                                <div class="w-2.5 h-2.5 rounded-sm bg-[#00f0ff]/50"></div>
                                <div class="w-2.5 h-2.5 rounded-sm bg-[#00f0ff]/75"></div>
                                <div class="w-2.5 h-2.5 rounded-sm bg-[#00f0ff]"></div>
                            </div>
                            <span>More</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-white/10">
                        <div class="text-xs font-mono text-slate-400 mb-3 uppercase">Latest Production Commits</div>
                        <div v-if="latestCommits.length" class="space-y-2">
                            <div v-for="(commit, idx) in latestCommits" :key="idx"
                                class="flex items-center justify-between text-xs font-mono p-2.5 rounded-xl bg-white/5 border border-white/5 hover:border-[#00f0ff]/30 transition-all">
                                <div class="flex items-center gap-2 truncate">
                                    <GitCommit class="w-3.5 h-3.5 text-[#00f0ff] shrink-0" />
                                    <span class="text-slate-200 font-bold shrink-0">{{ commit.repo }}</span>
                                    <span class="text-slate-400 truncate">{{ commit.message }}</span>
                                </div>
                                <span class="text-slate-500 text-[10px] shrink-0 ml-2">{{ commit.time }}</span>
                            </div>
                        </div>
                        <div v-else class="text-xs font-mono text-slate-500">No recent public commits found.</div>
                    </div>
                </div>

                <!-- Repo stats: stars, forks, language distribution -->
                <div class="glass-panel p-6 rounded-3xl border border-white/10">
                    <h4 class="text-lg font-bold text-white mb-4">Repo Stats</h4>

                    <div v-if="reposAvailable">
                        <div class="flex justify-between text-sm font-mono mb-6">
                            <span class="text-slate-300">⭐ {{ totalStars }} stars</span>
                            <span class="text-slate-300">🍴 {{ totalForks }} forks</span>
                        </div>

                        <div class="text-xs font-mono text-slate-400 mb-3 uppercase">Primary Stack Distribution</div>
                        <div v-if="topLanguages.length" class="space-y-2.5">
                            <div v-for="lang in topLanguages" :key="lang.name" class="text-xs font-mono">
                                <div class="flex justify-between text-slate-300 mb-1">
                                    <span>{{ lang.name }}</span>
                                    <span class="text-slate-400">{{ lang.percentage }}%</span>
                                </div>
                                <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000"
                                        :style="{ width: `${lang.percentage}%`, backgroundColor: lang.color }"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-xs font-mono text-slate-500">No language data on public repos yet.</div>
                    </div>
                    <div v-else class="text-xs font-mono text-slate-500">Repo stats unavailable right now.</div>
                </div>
            </div>
        </div>
    </section>
</template>
