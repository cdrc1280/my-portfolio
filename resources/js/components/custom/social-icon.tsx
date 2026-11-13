import type { LucideIcon, LucideProps } from 'lucide-react';
import { BriefcaseBusiness, Dribbble, Github, Globe, Linkedin, Mail, Twitter, Users, Youtube } from 'lucide-react';
import type { SocialPlatform } from '@/types';

const icons: Record<SocialPlatform, LucideIcon> = {
    github: Github,
    linkedin: Linkedin,
    upwork: BriefcaseBusiness,
    onlinejobs: Users,
    x: Twitter,
    email: Mail,
    website: Globe,
    youtube: Youtube,
    dribbble: Dribbble,
};

export function SocialIcon({ platform, ...props }: { platform: SocialPlatform } & LucideProps) {
    const Icon = icons[platform];

    return <Icon aria-hidden {...props} />;
}
