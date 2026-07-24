import { defineStore } from 'pinia';
import axios from 'axios';

export interface Project {
  id: number;
  title: string;
  slug: string;
  summary: string;
  description: string;
  hero_image: string;
  gallery?: string[];
  demo_url?: string;
  github_url?: string;
  tech_stack: string[];
  timeline?: string;
  features?: string[];
  architecture?: string;
  challenges?: string;
  solutions?: string;
  lessons_learned?: string;
  is_featured: boolean;
  stars_count: number;
  category?: { name: string; slug: string };
}

export interface Skill {
  id: number;
  name: string;
  icon?: string;
  proficiency: number;
  orbit_radius: number;
  color: string;
  years_of_experience: number;
  projects_count: number;
  category?: { name: string; slug: string };
}

export interface Experience {
  id: number;
  company: string;
  position: string;
  location?: string;
  start_date: string;
  end_date?: string;
  is_current: boolean;
  summary?: string;
  responsibilities?: string[];
  achievements?: string[];
  skills_used?: string[];
}

export interface Certificate {
  id: number;
  title: string;
  issuer: string;
  issue_date: string;
  credential_id?: string;
  credential_url?: string;
  category: string;
}

export interface Blog {
  id: number;
  title: string;
  slug: string;
  summary: string;
  content: string;
  cover_image?: string;
  reading_time: number;
  tags?: string[];
  published_at?: string;
  category?: { name: string; slug: string };
}

export const usePortfolioStore = defineStore('portfolio', {
  state: () => ({
    isLoading: true,
    settings: {} as Record<string, string>,
    projects: [] as Project[],
    projectCategories: [] as any[],
    skills: [] as Skill[],
    skillCategories: [] as any[],
    experiences: [] as Experience[],
    certificates: [] as Certificate[],
    blogs: [] as Blog[],
    services: [] as any[],
    testimonials: [] as any[],
    faqs: [] as any[],
    timeline: [] as any[],
    socialLinks: [] as any[],
    githubStats: null as any,
    selectedCategory: 'all',
    searchQuery: '',
    selectedProject: null as Project | null,
    selectedBlog: null as Blog | null,
  }),

  getters: {
    filteredProjects(state): Project[] {
      return state.projects.filter(p => {
        const matchesCategory = state.selectedCategory === 'all' || p.category?.slug === state.selectedCategory;
        const matchesSearch = !state.searchQuery || 
          p.title.toLowerCase().includes(state.searchQuery.toLowerCase()) ||
          p.summary.toLowerCase().includes(state.searchQuery.toLowerCase()) ||
          p.tech_stack.some(t => t.toLowerCase().includes(state.searchQuery.toLowerCase()));
        return matchesCategory && matchesSearch;
      });
    },

    featuredProjects(state): Project[] {
      return state.projects.filter(p => p.is_featured);
    }
  },

  actions: {
    async fetchPortfolioData() {
      this.isLoading = true;
      try {
        const response = await axios.get('/api/v1/portfolio');
        const data = response.data;
        this.settings = data.settings || {};
        this.projects = data.projects || [];
        this.projectCategories = data.project_categories || [];
        this.skills = data.skills || [];
        this.skillCategories = data.skill_categories || [];
        this.experiences = data.experiences || [];
        this.certificates = data.certificates || [];
        this.blogs = data.blogs || [];
        this.services = data.services || [];
        this.testimonials = data.testimonials || [];
        this.faqs = data.faqs || [];
        this.timeline = data.timeline || [];
        this.socialLinks = data.social_links || [];
      } catch (error) {
        console.error('Failed to fetch portfolio data:', error);
      } finally {
        this.isLoading = false;
      }
    },

    async fetchGithubStats() {
      try {
        const response = await axios.get('/api/v1/github/stats');
        this.githubStats = response.data;
      } catch (error) {
        console.error('Failed to fetch github stats:', error);
      }
    },

    setSelectedCategory(categorySlug: string) {
      this.selectedCategory = categorySlug;
    },

    openProjectModal(project: Project) {
      this.selectedProject = project;
    },

    closeProjectModal() {
      this.selectedProject = null;
    },

    openBlogModal(blog: Blog) {
      this.selectedBlog = blog;
    },

    closeBlogModal() {
      this.selectedBlog = null;
    }
  }
});
