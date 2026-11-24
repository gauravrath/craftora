import { youtubeChannelUrl } from '@/lib/home-content';

export { youtubeChannelUrl };

export const videoCategories = ['Toran Making', 'Diwali Decoration', 'Home Decor', 'Craft Tutorials', 'Beginner DIY'] as const;

export type VideoCategory = (typeof videoCategories)[number];

export type ChannelVideo = {
  // YouTube video ID. Leave empty until you have the real one.
  id: string;
  title: string;
  description: string;
  category: VideoCategory;
  featured?: boolean;
  // Used for the thumbnail until an ID is set; with an ID the YouTube thumbnail is used.
  fallbackThumbnail: string;
  // Opens a YouTube search when there is no ID yet.
  search: string;
  // Keywords used to match related products from the catalog.
  productKeywords: string[];
  // Search term for the "Shop Materials for This Project" link.
  materialSearch: string;
};

export function videoThumbnail(video: ChannelVideo) {
  return video.id ? `https://i.ytimg.com/vi/${video.id}/hqdefault.jpg` : video.fallbackThumbnail;
}

// PLACEHOLDER ENTRIES: replace titles, IDs, and descriptions with your real videos.
export const channelVideos: ChannelVideo[] = [
  {
    id: '',
    title: 'Make a statement toran, your way',
    description: 'A welcoming handmade detail for your doorway.',
    category: 'Toran Making',
    featured: true,
    fallbackThumbnail: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=85',
    search: 'handmade toran DIY tutorial',
    productKeywords: ['toran', 'bead', 'thread', 'kalawa'],
    materialSearch: 'toran',
  },
  {
    id: '',
    title: 'Festive decoration for Diwali',
    description: 'Bring a warm festive glow to your home with handmade pieces.',
    category: 'Diwali Decoration',
    fallbackThumbnail: 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=1000&q=85',
    search: 'DIY Diwali decoration tutorial',
    productKeywords: ['diwali', 'festive', 'lantern', 'diya'],
    materialSearch: 'festive',
  },
  {
    id: '',
    title: 'Decor accents made by hand',
    description: 'Bring texture and personality to your favorite corner.',
    category: 'Home Decor',
    fallbackThumbnail: 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=1000&q=85',
    search: 'handmade home decor DIY tutorial',
    productKeywords: ['decor', 'wall', 'table'],
    materialSearch: 'decor',
  },
  {
    id: '',
    title: 'A little color, a lot of joy',
    description: 'Turn simple materials into a bright keepsake.',
    category: 'Craft Tutorials',
    fallbackThumbnail: 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1000&q=85',
    search: 'colorful handmade craft DIY tutorial',
    productKeywords: ['kit', 'pearl', 'bead'],
    materialSearch: 'craft',
  },
  {
    id: '',
    title: 'Make something lovely from the basics',
    description: 'A relaxed project for your next creative afternoon.',
    category: 'Beginner DIY',
    fallbackThumbnail: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=1000&q=85',
    search: 'beginner craft project tutorial',
    productKeywords: ['kit', 'mdf'],
    materialSearch: 'kit',
  },
  {
    id: '',
    title: 'Beaded pieces step by step',
    description: 'Work with beads and pearls to make a handmade piece.',
    category: 'Craft Tutorials',
    fallbackThumbnail: 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?auto=format&fit=crop&w=1000&q=85',
    search: 'DIY beads pearls tutorial',
    productKeywords: ['bead', 'pearl'],
    materialSearch: 'beads',
  },
];
