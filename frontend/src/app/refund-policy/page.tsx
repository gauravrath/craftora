export const metadata = {
  title: 'Refund Policy',
  description: 'Craftora refund policy for eligible returns and damaged or incorrect orders.',
};

export default function RefundPolicyPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <h1 className="text-4xl font-bold text-white">Refund Policy</h1>
      <div className="mt-8 space-y-6 text-slate-300">
        <p>Refund eligibility depends on the item type, condition, and timing of the request.</p>
        <p>Damaged, incorrect, or non-conforming items will be reviewed promptly and resolved in line with our support process.</p>
        <p>Contact support to initiate a review for eligible returns or refund requests.</p>
      </div>
    </main>
  );
}
