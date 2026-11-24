const faqs = [
  {
    question: 'How long does shipping take?',
    answer: 'Most standard orders ship within 2–5 business days, depending on location and product availability.',
  },
  {
    question: 'Do you offer returns?',
    answer: 'Yes. Our return policy provides a clear process for eligible items and damaged shipments.',
  },
  {
    question: 'Are products covered by a warranty?',
    answer: 'Many products include product-specific warranty information in the product description and order confirmation.',
  },
  {
    question: 'Can I contact support?',
    answer: 'Yes, our support team is available to answer product questions and order support requests.',
  },
];

export const metadata = {
  title: 'FAQ',
  description: 'Answers to common questions about shipping, returns, support, and product details.',
};

export default function FAQPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <p className="text-xs font-medium uppercase tracking-[0.24em] text-emerald-300">Frequently asked questions</p>
      <h1 className="mt-4 text-4xl font-bold text-white">Questions, answered clearly</h1>
      <div className="mt-10 space-y-5">
        {faqs.map((faq) => (
          <div key={faq.question} className="rounded-3xl border border-slate-800 bg-slate-900/70 p-5">
            <h2 className="text-xl font-semibold text-white">{faq.question}</h2>
            <p className="mt-3 text-slate-300">{faq.answer}</p>
          </div>
        ))}
      </div>
    </main>
  );
}
