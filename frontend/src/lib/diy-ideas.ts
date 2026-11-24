export const ideaTypes = [
  'Festival Crafts',
  'Home Decor DIY',
  'Handmade Gifts',
  'Beginner Projects',
  'Budget-Friendly Crafts',
] as const;

export type IdeaType = (typeof ideaTypes)[number];

export type DiyIdea = {
  slug: string;
  title: string;
  description: string;
  image: string;
  imageAlt: string;
  type: IdeaType;
  occasion?: string;
  // Set only when verified for the tutorial.
  difficulty?: 'Beginner' | 'Intermediate' | 'Advanced';
  timeEstimate?: string;
  // Add the video ID to link the exact tutorial; otherwise a YouTube search is used.
  youtubeId?: string;
  youtubeSearch: string;
  // Keywords used to find related products in the catalog.
  productKeywords: string[];
  // Search term for the "Shop Materials" link.
  materialSearch: string;
};

// SAMPLE CONTENT: replace these entries with your real tutorials.
export const diyIdeas: DiyIdea[] = [
  {
    slug: 'diwali-toran',
    title: 'Festive doorway toran',
    description: 'A handmade toran to welcome guests during the festive season.',
    image: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=85',
    imageAlt: 'Decorative handmade details in a styled home',
    type: 'Festival Crafts',
    occasion: 'Diwali',
    youtubeSearch: 'DIY Diwali toran tutorial',
    productKeywords: ['toran', 'bead', 'thread', 'kalawa'],
    materialSearch: 'toran',
  },
  {
    slug: 'festive-lights-decor',
    title: 'Festive light and lantern decor',
    description: 'Bring a warm glow to your space with handmade festive pieces.',
    image: 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=1000&q=85',
    imageAlt: 'Colorful festive confetti',
    type: 'Festival Crafts',
    occasion: 'Diwali',
    youtubeSearch: 'DIY festive lantern decoration',
    productKeywords: ['lantern', 'diya', 'festive', 'mirror'],
    materialSearch: 'festive',
  },
  {
    slug: 'corner-decor-accents',
    title: 'Handmade corner accents',
    description: 'Add texture and personality to a favorite corner of your home.',
    image: 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=1000&q=85',
    imageAlt: 'Warm home interior with handmade decoration',
    type: 'Home Decor DIY',
    youtubeSearch: 'handmade home decor DIY tutorial',
    productKeywords: ['decor', 'wall', 'table', 'frame'],
    materialSearch: 'decor',
  },
  {
    slug: 'mdf-wall-hanging',
    title: 'Painted MDF wall hanging',
    description: 'Turn plain MDF shapes into a personal piece of wall art.',
    image: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=1000&q=85',
    imageAlt: 'Paint and craft tools ready for a project',
    type: 'Home Decor DIY',
    youtubeSearch: 'DIY MDF wall hanging painting',
    productKeywords: ['mdf', 'paint', 'wall'],
    materialSearch: 'MDF',
  },
  {
    slug: 'beaded-keepsake-gift',
    title: 'Beaded keepsake gift',
    description: 'A thoughtful handmade gift made with beads and pearls.',
    image: 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?auto=format&fit=crop&w=1000&q=85',
    imageAlt: 'Handmade bead jewelry in warm colors',
    type: 'Handmade Gifts',
    occasion: 'Gifting',
    youtubeSearch: 'DIY beaded gift handmade',
    productKeywords: ['bead', 'pearl', 'gift'],
    materialSearch: 'beads',
  },
  {
    slug: 'first-craft-kit',
    title: 'Your first craft kit project',
    description: 'A relaxed start for anyone new to making things by hand.',
    image: 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1000&q=85',
    imageAlt: 'Colorful craft supplies arranged on a worktable',
    type: 'Beginner Projects',
    youtubeSearch: 'beginner DIY craft kit tutorial',
    productKeywords: ['kit'],
    materialSearch: 'kit',
  },
  {
    slug: 'simple-thread-decor',
    title: 'Simple decor from everyday thread',
    description: 'Make something lovely with a few low-cost supplies.',
    image: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=85',
    imageAlt: 'Decorative handmade details',
    type: 'Budget-Friendly Crafts',
    youtubeSearch: 'budget friendly DIY craft decor',
    productKeywords: ['thread', 'kalawa', 'cord'],
    materialSearch: 'thread',
  },
];
