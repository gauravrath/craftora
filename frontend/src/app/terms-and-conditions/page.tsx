export const metadata = {
  title: 'Terms & Conditions',
  description: 'Craftora terms and conditions for online shopping and account usage.',
};

export default function TermsAndConditionsPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <h1 className="text-4xl font-bold text-white">Terms & Conditions</h1>
      <div className="mt-8 space-y-6 text-slate-300">
        <p>By using this storefront, you agree to provide accurate information and comply with the applicable laws related to online commerce.</p>
        <p>Product availability, pricing, and order fulfillment are subject to change based on stock or operational conditions.</p>
        <p>Please review product details carefully before purchase and contact support with any questions.</p>
      </div>
    </main>
  );
}
