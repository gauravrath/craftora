export const materialGroups = [
  {
    key: 'beads-pearls',
    name: 'Beads & Pearls',
    keywords: ['bead', 'pearl'],
    image: 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?auto=format&fit=crop&w=900&q=85',
    alt: 'Handmade bead jewelry in warm colors',
    tone: 'bg-[#f1e8c9]',
  },
  {
    key: 'threads-kalawa',
    name: 'Decorative Threads & Kalawa',
    keywords: ['thread', 'kalawa', 'moli', 'yarn', 'cord'],
    image: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=900&q=85',
    alt: 'Decorative handmade details in a thoughtfully styled home',
    tone: 'bg-[#f2dfd7]',
  },
  {
    key: 'mdf',
    name: 'MDF Shapes & Bases',
    keywords: ['mdf', 'base', 'wooden', 'cutout'],
    image: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=900&q=85',
    alt: 'Paint and craft tools ready for a handmade project',
    tone: 'bg-[#e4eadc]',
  },
  {
    key: 'mirrors-embellishments',
    name: 'Mirrors & Embellishments',
    keywords: ['mirror', 'embellish', 'sequin', 'stone', 'lace', 'charm'],
    image: 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=900&q=85',
    alt: 'Colorful festive confetti',
    tone: 'bg-[#efe2c8]',
  },
  {
    key: 'tools',
    name: 'Craft Tools',
    keywords: ['tool', 'scissor', 'cutter', 'brush', 'plier'],
    image: 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=900&q=85',
    alt: 'Colorful craft supplies arranged on a worktable',
    tone: 'bg-[#f4e4d6]',
  },
  {
    key: 'adhesives-accessories',
    name: 'Adhesives & Accessories',
    keywords: ['glue', 'adhesive', 'tape', 'accessor', 'hook', 'clasp'],
    image: 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85',
    alt: 'Warm home interior with artisan-inspired decoration',
    tone: 'bg-[#e9e5dc]',
  },
];

export function productMatchesGroup(
  product: { name: string; short_description?: string | null; category?: { name?: string } | null },
  group: { keywords: string[] },
) {
  const text = `${product.name} ${product.category?.name ?? ''} ${product.short_description ?? ''}`.toLowerCase();
  return group.keywords.some((keyword) => text.includes(keyword));
}
