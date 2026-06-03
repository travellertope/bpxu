'use client';

import { Suspense, useEffect, useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';

declare global {
    interface Window {
        grecaptcha: {
            ready: (cb: () => void) => void;
            execute: (siteKey: string, opts: { action: string }) => Promise<string>;
        };
    }
}

const SITE_KEY = process.env.NEXT_PUBLIC_RECAPTCHA_SITE_KEY || '';

type FormData = {
  // Step 1 – Account
  username: string;
  email: string;
  password: string;
  confirm_password: string;
  // Step 2 – Personal
  first_name: string;
  last_name: string;
  phone_number: string;
  birthday: string;
  gender: string;
  sexuality: string;
  // Step 3 – Background
  education: string;
  ethnicity: string;
  first_gen_immigrant: string;
  disability: string;
  other_disability: string;
  // Step 4 – Career
  employment_status: string;
  industry: string;
  field_of_expertise: string;
  expertise_not_listed: string;
  years_experience: string;
  skills: string;
  // Step 5 – Location & Bio
  country: string;
  where_in_uk: string;
  city: string;
  user_bio: string;
};

const EMPTY: FormData = {
  username: '', email: '', password: '', confirm_password: '',
  first_name: '', last_name: '', phone_number: '', birthday: '', gender: '', sexuality: '',
  education: '', ethnicity: '', first_gen_immigrant: '', disability: '', other_disability: '',
  employment_status: '', industry: '', field_of_expertise: '', expertise_not_listed: '', years_experience: '', skills: '',
  country: '', where_in_uk: '', city: '', user_bio: '',
};

const INDUSTRIES = [
  'Accounting',
  'Administration & Office Support',
  'Advertising, Arts & Media',
  'Banking & Financial Services',
  'Call Centre & Customer Service',
  'Community Services & Development',
  'Construction',
  'Consulting & Strategy',
  'Education & Training',
  'Engineering',
  'Farming, Animals & Conservation',
  'Government & Defence',
  'Healthcare & Medical',
  'Hospitality & Tourism',
  'Human Resources & Recruitment',
  'Information & Communication Technology',
  'Insurance & Superannuation',
  'Legal',
  'Manufacturing, Transport & Logistics',
  'Marketing & Communications',
  'Mining, Resources & Energy',
  'Real Estate & Property',
  'Retail & Consumer Products',
  'Sales',
  'Science & Technology',
  'Self Employment',
  'Sport & Recreation',
  'Trades & Services',
  'Other',
];

const FIELDS_OF_EXPERTISE = [
  'Accounting & Finance',
  'Administration',
  'Arts & Design',
  'Business Development',
  'Consulting',
  'Customer Service',
  'Data & Analytics',
  'Education',
  'Engineering',
  'Healthcare',
  'HR & Recruitment',
  'IT & Software',
  'Law & Legal Services',
  'Logistics & Supply Chain',
  'Management',
  'Marketing & Communications',
  'Media & Journalism',
  'Operations',
  'Policy & Government',
  'Project Management',
  'Property & Real Estate',
  'Research & Science',
  'Sales',
  'Social Work & Community',
  'Sport & Fitness',
  'Other',
];

const COUNTRIES = [
  'United Kingdom',
  'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda',
  'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain',
  'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan',
  'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria',
  'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia', 'Cameroon', 'Canada',
  'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros',
  'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark',
  'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador',
  'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland',
  'France', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada',
  'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Hungary',
  'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy',
  'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait',
  'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya',
  'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia',
  'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico',
  'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique',
  'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua',
  'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan',
  'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines',
  'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis',
  'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino',
  'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles',
  'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia',
  'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan',
  'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania',
  'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia',
  'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates',
  'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela',
  'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe',
];

const STEP_TITLES = [
  'Account',
  'Personal',
  'Background',
  'Career',
  'Location & Bio',
];

// ── Module-level form helpers ─────────────────────────────────────────────────
// IMPORTANT: must live outside RegisterForm so React sees a stable component
// identity on every render and does not unmount/remount inputs (focus loss).

function Field({
  id, label, type = 'text', placeholder, value, onChange, required = true, autoComplete, disabled,
}: {
  id: string;
  label: string;
  type?: string;
  placeholder?: string;
  value: string;
  onChange: (v: string) => void;
  required?: boolean;
  autoComplete?: string;
  disabled?: boolean;
}) {
  return (
    <div>
      <label htmlFor={id} className="field-label">{label}</label>
      <input
        id={id}
        type={type}
        autoComplete={autoComplete}
        className="field-input w-full"
        placeholder={placeholder}
        value={value}
        onChange={e => onChange(e.target.value)}
        required={required}
        disabled={disabled}
      />
    </div>
  );
}

function Select({
  id, label, value, onChange, options, placeholder = 'Select…', required = true, disabled,
}: {
  id: string;
  label: string;
  value: string;
  onChange: (v: string) => void;
  options: string[];
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
}) {
  return (
    <div>
      <label htmlFor={id} className="field-label">{label}</label>
      <select
        id={id}
        className="field-input w-full"
        value={value}
        onChange={e => onChange(e.target.value)}
        required={required}
        disabled={disabled}
      >
        <option value="">{placeholder}</option>
        {options.map(o => (
          <option key={o} value={o}>{o}</option>
        ))}
      </select>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────────────────

function RegisterForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const returnTo = searchParams.get('returnTo') || '/';

  const [step, setStep] = useState(1);
  const [form, setForm] = useState<FormData>(EMPTY);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  // Load reCAPTCHA v3 script
  useEffect(() => {
    if (!SITE_KEY || document.getElementById('recaptcha-script')) return;
    const script = document.createElement('script');
    script.id = 'recaptcha-script';
    script.src = `https://www.google.com/recaptcha/api.js?render=${SITE_KEY}`;
    script.async = true;
    document.head.appendChild(script);
  }, []);

  async function getRecaptchaToken(action: string): Promise<string> {
    if (!SITE_KEY || typeof window === 'undefined' || !window.grecaptcha) return '';
    return new Promise(resolve => {
        window.grecaptcha.ready(() => {
            window.grecaptcha.execute(SITE_KEY, { action }).then(resolve).catch(() => resolve(''));
        });
    });
  }

  function set(field: keyof FormData, value: string) {
    setForm(prev => ({ ...prev, [field]: value }));
  }

  function validateStep(): string {
    if (step === 1) {
      if (!form.username.trim()) return 'Username is required.';
      if (!form.email.trim()) return 'Email is required.';
      if (!form.password) return 'Password is required.';
      if (form.password.length < 8) return 'Password must be at least 8 characters.';
      if (form.password !== form.confirm_password) return 'Passwords do not match.';
    }
    if (step === 2) {
      if (!form.first_name.trim()) return 'First name is required.';
      if (!form.last_name.trim()) return 'Last name is required.';
      if (!form.gender) return 'Please select your gender.';
      if (!form.sexuality) return 'Please select your sexuality.';
    }
    if (step === 3) {
      if (!form.education) return 'Please select your education level.';
      if (!form.ethnicity) return 'Please select your ethnicity.';
      if (!form.first_gen_immigrant) return 'Please answer the first-generation immigrant question.';
      if (!form.disability) return 'Please select a disability option.';
    }
    if (step === 4) {
      if (!form.employment_status) return 'Please select your employment status.';
      if (!form.industry) return 'Please select your industry.';
      if (!form.field_of_expertise) return 'Please select your field of expertise.';
      if (!form.years_experience) return 'Please select your years of experience.';
    }
    if (step === 5) {
      if (!form.country) return 'Please select your country.';
      if (form.country === 'United Kingdom' && !form.where_in_uk) return 'Please select where in the UK you are based.';
    }
    return '';
  }

  function handleNext(e: React.FormEvent) {
    e.preventDefault();
    const err = validateStep();
    if (err) { setError(err); return; }
    setError('');
    setStep(s => s + 1);
  }

  function handleBack() {
    setError('');
    setStep(s => s - 1);
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const err = validateStep();
    if (err) { setError(err); return; }
    setError('');
    setLoading(true);

    const payload: Record<string, unknown> = { ...form };
    // Remove confirm_password — not sent to backend
    delete payload.confirm_password;
    // Strip conditionally hidden fields if not applicable
    if (form.disability.toLowerCase() !== 'others') payload.other_disability = '';
    if (form.field_of_expertise !== 'Other') payload.expertise_not_listed = '';
    if (form.country !== 'United Kingdom') payload.where_in_uk = '';

    try {
      payload.recaptcha_token = await getRecaptchaToken('register');

      const res = await fetch('/api/auth/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        setError(data.error || 'Registration failed. Please try again.');
        setLoading(false);
        return;
      }

      router.push(returnTo);
    } catch {
      setError('Something went wrong. Please try again.');
      setLoading(false);
    }
  }

  const loginHref = `/login${returnTo !== '/' ? `?returnTo=${encodeURIComponent(returnTo)}` : ''}`;

  const yearsOptions = [
    ...Array.from({ length: 40 }, (_, i) => String(i + 1)),
    '40+',
  ];

  return (
    <main className="min-h-screen flex">

      {/* ── Left panel: image + brand ─────────────────────────────── */}
      <div className="hidden lg:flex lg:w-[40%] relative flex-col overflow-hidden sticky top-0 h-screen">
        <div
          className="absolute inset-0 bg-cover bg-center bg-no-repeat"
          style={{ backgroundImage: `url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80')` }}
        />
        <div
          className="absolute inset-0"
          style={{ background: 'linear-gradient(160deg,rgba(0,0,0,0.90) 0%,rgba(200,16,46,0.35) 100%)' }}
        />
        <div className="relative z-10 flex flex-col justify-between h-full p-12">
          <img
            src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
            alt="Black Professionals United"
            className="h-10 w-auto self-start brightness-0 invert"
          />
          <div>
            <h2 className="text-4xl font-extrabold text-white leading-[1.15] mb-4 tracking-tight">
              Join 7,000+<br />Black<br />Professionals.
            </h2>
            <p className="text-white/65 leading-relaxed mb-8 text-sm max-w-xs">
              Access job opportunities, AI-powered CV tools, accredited courses, and a thriving professional community built for you.
            </p>
            <div className="space-y-3">
              {[
                '✓  Free job matching & recommendations',
                '✓  AI CV Clinic & career tools',
                '✓  Mentorship via PAIRED',
                '✓  Professional courses & events',
              ].map(f => (
                <p key={f} className="text-white/80 text-sm">{f}</p>
              ))}
            </div>
          </div>
          <p className="text-white/25 text-xs">Photo: Unsplash / Brooke Cagle</p>
        </div>
      </div>

      {/* ── Right panel: form (scrollable) ────────────────────────── */}
      <div className="flex-1 bg-bg overflow-y-auto">
        <div className="flex flex-col items-center py-10 px-6 min-h-full">
          <div className="w-full max-w-lg fade-up">

            {/* Logo — mobile only */}
            <div className="lg:hidden text-center mb-8">
              <img
                src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
                alt="Black Professionals United"
                className="h-12 w-auto mx-auto mb-2"
              />
              <p className="text-xs text-text-3 font-medium uppercase tracking-widest">Member Portal</p>
            </div>

            <div className="card card-p space-y-6">

          {/* Progress header */}
          <div className="space-y-3">
            <div className="flex items-center justify-between">
              <h1 className="text-xl font-bold">Create your account</h1>
              <span className="text-sm text-text-2 font-medium">Step {step} of 5</span>
            </div>
            <div className="w-full h-1.5 rounded-full bg-border overflow-hidden">
              <div
                className="h-full rounded-full bg-brand transition-all duration-300"
                style={{ width: `${(step / 5) * 100}%` }}
              />
            </div>
            <p className="text-xs font-semibold text-text-3 uppercase tracking-wider">
              {STEP_TITLES[step - 1]}
            </p>
          </div>

          <div className="divider" />

          {/* Error banner */}
          {error && (
            <div className="alert alert-red text-sm">
              {error}
            </div>
          )}

          {/* ── Step 1: Account ─────────────────────────────── */}
          {step === 1 && (
            <form onSubmit={handleNext} className="space-y-4">
              <Field
                id="username" label="Username" placeholder="Choose a username"
                value={form.username} onChange={v => set('username', v)}
                autoComplete="username" disabled={loading}
              />
              <Field
                id="email" label="Email address" type="email" placeholder="you@example.com"
                value={form.email} onChange={v => set('email', v)}
                autoComplete="email" disabled={loading}
              />
              <Field
                id="password" label="Password" type="password" placeholder="Min. 8 characters"
                value={form.password} onChange={v => set('password', v)}
                autoComplete="new-password" disabled={loading}
              />
              <Field
                id="confirm_password" label="Confirm password" type="password" placeholder="Repeat password"
                value={form.confirm_password} onChange={v => set('confirm_password', v)}
                autoComplete="new-password" disabled={loading}
              />
              <div className="flex justify-end pt-2">
                <button type="submit" className="btn btn-amber btn-lg" disabled={loading}>
                  Next
                </button>
              </div>
            </form>
          )}

          {/* ── Step 2: Personal ────────────────────────────── */}
          {step === 2 && (
            <form onSubmit={handleNext} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <Field
                  id="first_name" label="First name" placeholder="Jane"
                  value={form.first_name} onChange={v => set('first_name', v)}
                  autoComplete="given-name" disabled={loading}
                />
                <Field
                  id="last_name" label="Last name" placeholder="Smith"
                  value={form.last_name} onChange={v => set('last_name', v)}
                  autoComplete="family-name" disabled={loading}
                />
              </div>
              <Field
                id="phone_number" label="Phone number" type="tel" placeholder="+44 7700 000000"
                value={form.phone_number} onChange={v => set('phone_number', v)}
                required={false} autoComplete="tel" disabled={loading}
              />
              <Field
                id="birthday" label="Date of birth" type="date"
                value={form.birthday} onChange={v => set('birthday', v)}
                required={false} disabled={loading}
              />
              <Select
                id="gender" label="Gender"
                value={form.gender} onChange={v => set('gender', v)}
                options={['Male', 'Female', 'Prefer Not to Say', 'Others']}
                disabled={loading}
              />
              <Select
                id="sexuality" label="Sexuality"
                value={form.sexuality} onChange={v => set('sexuality', v)}
                options={['Asexual', 'Bisexual', 'Gay', 'Intersex', 'Lesbian', 'Queer', 'Straight', 'Transgender', 'Prefer not to say']}
                disabled={loading}
              />
              <div className="flex justify-between pt-2">
                <button type="button" className="btn btn-outline btn-lg" onClick={handleBack} disabled={loading}>
                  Back
                </button>
                <button type="submit" className="btn btn-amber btn-lg" disabled={loading}>
                  Next
                </button>
              </div>
            </form>
          )}

          {/* ── Step 3: Background ──────────────────────────── */}
          {step === 3 && (
            <form onSubmit={handleNext} className="space-y-4">
              <Select
                id="education" label="Highest level of education"
                value={form.education} onChange={v => set('education', v)}
                options={["High School", "Bachelor's Degree", "Professional Qualification", "Masters Degree", "PhD", "Other", "Prefer Not to Answer"]}
                disabled={loading}
              />
              <Select
                id="ethnicity" label="Ethnicity"
                value={form.ethnicity} onChange={v => set('ethnicity', v)}
                options={['African', 'Asian', 'Black British', 'Black Caribbean', 'Gypsy or Irish Traveller', 'Hispanic', 'Mixed Ethnic Group', 'Other Black Background', 'White']}
                disabled={loading}
              />
              <Select
                id="first_gen_immigrant" label="Are you a first-generation immigrant?"
                value={form.first_gen_immigrant} onChange={v => set('first_gen_immigrant', v)}
                options={['Yes', 'No']}
                disabled={loading}
              />
              <Select
                id="disability" label="Disability"
                value={form.disability} onChange={v => set('disability', v)}
                options={['No Disability', 'Cognitive or learning disability', 'Hearing impairment', 'Mobility impairment', 'Visual impairment', 'Others (Input below)']}
                disabled={loading}
              />
              {form.disability === 'Others (Input below)' && (
                <Field
                  id="other_disability" label="Please describe your disability" placeholder="Describe your disability…"
                  value={form.other_disability} onChange={v => set('other_disability', v)}
                  required={false} disabled={loading}
                />
              )}
              <div className="flex justify-between pt-2">
                <button type="button" className="btn btn-outline btn-lg" onClick={handleBack} disabled={loading}>
                  Back
                </button>
                <button type="submit" className="btn btn-amber btn-lg" disabled={loading}>
                  Next
                </button>
              </div>
            </form>
          )}

          {/* ── Step 4: Career ──────────────────────────────── */}
          {step === 4 && (
            <form onSubmit={handleNext} className="space-y-4">
              <Select
                id="employment_status" label="Employment status"
                value={form.employment_status} onChange={v => set('employment_status', v)}
                options={['Employed Full-Time', 'Employed Part-Time', 'Self-employed', 'Not employed but looking for work', 'Not employed and not looking for work', 'Retired', 'Student', 'Prefer Not to Answer']}
                disabled={loading}
              />
              <Select
                id="industry" label="Industry"
                value={form.industry} onChange={v => set('industry', v)}
                options={INDUSTRIES}
                disabled={loading}
              />
              <Select
                id="field_of_expertise" label="Field of expertise"
                value={form.field_of_expertise} onChange={v => set('field_of_expertise', v)}
                options={FIELDS_OF_EXPERTISE}
                disabled={loading}
              />
              {form.field_of_expertise === 'Other' && (
                <Field
                  id="expertise_not_listed" label="Please specify your field of expertise" placeholder="Your field…"
                  value={form.expertise_not_listed} onChange={v => set('expertise_not_listed', v)}
                  required={false} disabled={loading}
                />
              )}
              <Select
                id="years_experience" label="Years of experience"
                value={form.years_experience} onChange={v => set('years_experience', v)}
                options={yearsOptions}
                disabled={loading}
              />
              <Field
                id="skills" label="Key skills" placeholder="e.g. Python, Leadership, Project Management"
                value={form.skills} onChange={v => set('skills', v)}
                required={false} disabled={loading}
              />
              <div className="flex justify-between pt-2">
                <button type="button" className="btn btn-outline btn-lg" onClick={handleBack} disabled={loading}>
                  Back
                </button>
                <button type="submit" className="btn btn-amber btn-lg" disabled={loading}>
                  Next
                </button>
              </div>
            </form>
          )}

          {/* ── Step 5: Location & Bio ──────────────────────── */}
          {step === 5 && (
            <form onSubmit={handleSubmit} className="space-y-4">
              <Select
                id="country" label="Country"
                value={form.country} onChange={v => set('country', v)}
                options={COUNTRIES}
                disabled={loading}
              />
              {form.country === 'United Kingdom' && (
                <Select
                  id="where_in_uk" label="Where in the UK?"
                  value={form.where_in_uk} onChange={v => set('where_in_uk', v)}
                  options={['England', 'Scotland', 'Wales', 'Northern Ireland']}
                  disabled={loading}
                />
              )}
              <Field
                id="city" label="City" placeholder="e.g. London"
                value={form.city} onChange={v => set('city', v)}
                required={false} autoComplete="address-level2" disabled={loading}
              />
              <div>
                <label htmlFor="user_bio" className="field-label">About You</label>
                <textarea
                  id="user_bio"
                  className="field-input field-textarea w-full"
                  placeholder="Tell other members a bit about yourself, your career journey, and what you're looking for…"
                  value={form.user_bio}
                  onChange={e => set('user_bio', e.target.value)}
                  disabled={loading}
                />
              </div>
              <div className="flex justify-between pt-2">
                <button type="button" className="btn btn-outline btn-lg" onClick={handleBack} disabled={loading}>
                  Back
                </button>
                <button type="submit" className="btn btn-amber btn-lg" disabled={loading}>
                  {loading ? 'Creating account…' : 'Create account'}
                </button>
              </div>
            </form>
          )}

            <p className="text-center text-sm text-text-2">
              Already have an account?{' '}
              <a href={loginHref} className="font-semibold text-brand-dark hover:underline">
                Sign in
              </a>
            </p>
            </div>

            <p className="mt-6 text-center text-xs text-text-3">
              Empowering Black professionals in the UK
            </p>
          </div>
        </div>
      </div>

    </main>
  );
}

export default function RegisterPage() {
  return (
    <Suspense fallback={
      <main className="min-h-screen flex items-center justify-center bg-bg">
        <div className="text-sm text-text-2">Loading…</div>
      </main>
    }>
      <RegisterForm />
    </Suspense>
  );
}
