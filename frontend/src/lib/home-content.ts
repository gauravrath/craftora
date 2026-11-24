export const youtubeChannelUrl = 'https://www.youtube.com/@Anjalicreativestu';

export const craftCategories = [
  {
    name: 'DIY Craft Kits',
    href: '/diy-kits',
    search: 'DIY craft kit',
    image: 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=900&q=85',
    alt: 'Colorful craft supplies arranged on a worktable',
    tone: 'bg-[#f4e4d6]',
  },
  {
    name: 'Beads & Pearls',
    href: '/craft-materials?group=beads-pearls',
    search: 'beads pearls',
    image: 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?auto=format&fit=crop&w=900&q=85',
    alt: 'Handmade bead jewelry in warm colors',
    tone: 'bg-[#f1e8c9]',
  },
  {
    name: 'MDF Craft Items',
    href: '/craft-materials?group=mdf',
    search: 'MDF craft',
    image: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=900&q=85',
    alt: 'Paint and craft tools ready for a handmade project',
    tone: 'bg-[#e4eadc]',
  },
  {
    name: 'Toran & Wall Hangings',
    href: '/home-decor?group=torans-door-hangings',
    search: 'toran wall hanging',
    image: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=900&q=85',
    alt: 'Decorative handmade details in a thoughtfully styled home',
    tone: 'bg-[#f2dfd7]',
  },
  {
    name: 'Home Decoration',
    href: '/home-decor',
    search: 'home decoration',
    image: 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85',
    alt: 'Warm home interior with artisan-inspired decoration',
    tone: 'bg-[#e9e5dc]',
  },
  {
    name: 'Festival Decoration',
    href: '/home-decor?group=festive-decor',
    search: 'festival decoration',
    image: 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=900&q=85',
    alt: 'Colorful festive confetti',
    tone: 'bg-[#efe2c8]',
  },
];

export const diyVideos = [
  {
    id: '',
    title: 'Make a statement toran, your way',
    description: 'A welcoming handmade detail for your doorway.',
    thumbnail: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=85',
    search: 'handmade toran DIY tutorial',
  },
  {
    id: '',
    title: 'A little color, a lot of joy',
    description: 'Turn simple materials into a bright keepsake.',
    thumbnail: 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1000&q=85',
    search: 'colorful handmade craft DIY tutorial',
  },
  {
    id: '',
    title: 'Decor accents made by hand',
    description: 'Bring texture and personality to your favorite corner.',
    thumbnail: 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=1000&q=85',
    search: 'handmade home decor DIY tutorial',
  },
  {
    id: '',
    title: 'Make something lovely from the basics',
    description: 'A relaxed project for your next creative afternoon.',
    thumbnail: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=1000&q=85',
    search: 'beginner craft project tutorial',
  },
];

export const videoProjects = [
  {
    title: 'The everyday doorway toran',
    description: 'Layer color, texture, and handmade detail for a welcoming entrance.',
    videoIndex: 0,
    materials: ['beads', 'decorative thread', 'MDF shapes', 'DIY kit'],
    search: 'toran craft materials',
  },
  {
    title: 'A bright handmade keepsake',
    description: 'A thoughtful little project to make and gift.',
    videoIndex: 1,
    materials: ['craft kit', 'pearls', 'decorative accents'],
    search: 'handmade gift craft materials',
  },
];

export const diyIdeas = [
  { title: 'Diwali Decoration Ideas', eyebrow: 'For festive homes', search: 'Diwali decoration DIY' },
  { title: 'Handmade Gift Ideas', eyebrow: 'Made with meaning', search: 'handmade gift DIY' },
  { title: 'Budget-Friendly Home Decor', eyebrow: 'Small changes, lovely spaces', search: 'budget home decor DIY' },
  { title: 'Beginner DIY Projects', eyebrow: 'Start creating today', search: 'beginner DIY craft' },
];

export function youtubeVideoUrl(video: (typeof diyVideos)[number]) {
  return video.id
    ? `https://www.youtube.com/watch?v=${video.id}`
    : `https://www.youtube.com/results?search_query=${encodeURIComponent(video.search)}`;
}