import type { LucideIcon, LucideProps } from 'lucide-react';
import { Dribbble, Github, Globe, Linkedin, Mail, Twitter, Youtube } from 'lucide-react';
import type { SocialPlatform } from '@/types';

const icons: Record<SocialPlatform, LucideIcon> = {
    github: Github,
    linkedin: Linkedin,
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
