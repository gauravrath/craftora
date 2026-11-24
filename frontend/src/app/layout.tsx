import type { Metadata } from 'next';
import './globals.css';
import { Header } from '@/components/storefront/Header';
import { Footer } from '@/components/storefront/Footer';

export const metadata: Metadata = {
  title: {
    default: 'Craftora | Handmade Finds & DIY Inspiration',
    template: '%s | Craftora',
  },
  description: 'Discover handmade decor, thoughtful DIY kits, and craft materials to bring your creative ideas to life.',
  keywords: ['handmade crafts', 'DIY kits', 'craft materials', 'home decor', 'DIY tutorials'],
  openGraph: {
    title: 'Craftora | Handmade Finds & DIY Inspiration',
    description: 'Shop handmade decor and craft materials, then make something lovely with Craftora DIY tutorials.',
    siteName: 'Craftora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Craftora | Handmade Finds & DIY Inspiration',
    description: 'Shop handmade decor and craft materials, then make something lovely with Craftora DIY tutorials.',
  },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body className="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <Header />
        {children}
        <Footer />
      </body>
    </html>
  );
}
