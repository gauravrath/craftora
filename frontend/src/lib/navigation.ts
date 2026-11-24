export const mainNavigation = [
  { href: '/', label: 'Home' },
  { href: '/shop', label: 'Shop All' },
  { href: '/diy-kits', label: 'DIY Kits' },
  { href: '/craft-materials', label: 'Craft Materials' },
  { href: '/home-decor', label: 'Home Decor' },
  { href: '/diy-ideas', label: 'DIY Ideas' },
  { href: '/diy-videos', label: 'DIY Videos' },
];

export function isActiveRoute(pathname: string, href: string) {
  if (href === '/') return pathname === '/';
  if (href === '/shop') return pathname === '/shop' || pathname === '/products' || pathname.startsWith('/products/');
  return pathname === href || pathname.startsWith(`${href}/`);
}
