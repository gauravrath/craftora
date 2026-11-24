export const metadata = {
  title: 'Shipping Policy',
  description: 'Craftora shipping and delivery policy for domestic orders and fulfillment timelines.',
};

export default function ShippingPolicyPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <h1 className="text-4xl font-bold text-white">Shipping Policy</h1>
      <div className="mt-8 space-y-6 text-slate-300">
        <p>Orders are processed within the stated fulfillment window and shipped using trusted carriers.</p>
        <p>Delivery times vary by destination, shipping method, and product availability.</p>
        <p>Tracking information is provided where available and support can assist with order status questions.</p>
      </div>
    </main>
  );
}
