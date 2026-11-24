export const metadata = {
  title: 'Privacy Policy',
  description: 'Craftora privacy policy for product purchases, account information, and communication preferences.',
};

export default function PrivacyPolicyPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <h1 className="text-4xl font-bold text-white">Privacy Policy</h1>
      <div className="mt-8 space-y-6 text-slate-300">
        <p>We collect only the information needed to provide a secure and efficient shopping experience.</p>
        <p>Personal data is used for order processing, account support, and service communication.</p>
        <p>We do not sell personal information and apply security measures appropriate for a digital commerce environment.</p>
      </div>
    </main>
  );
}
