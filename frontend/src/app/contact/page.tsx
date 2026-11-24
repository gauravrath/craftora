export const metadata = {
  title: 'Contact',
  description: 'Contact Craftora for product support, shipping, and general questions.',
};

export default function ContactPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <p className="text-xs font-medium uppercase tracking-[0.24em] text-emerald-300">Contact</p>
      <h1 className="mt-4 text-4xl font-bold text-white">We’re here to help</h1>
      <div className="mt-10 grid gap-6 md:grid-cols-2">
        <div className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
          <p className="text-sm uppercase tracking-[0.2em] text-slate-400">Email</p>
          <p className="mt-3 text-xl text-white">support@craftora.io</p>
        </div>
        <div className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
          <p className="text-sm uppercase tracking-[0.2em] text-slate-400">Hours</p>
          <p className="mt-3 text-xl text-white">Mon–Fri, 9am–6pm</p>
        </div>
      </div>
    </main>
  );
}
