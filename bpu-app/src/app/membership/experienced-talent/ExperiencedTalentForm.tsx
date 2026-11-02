'use client';

import { useState, useRef } from 'react';

const UK_CITIES = [
    'Aberdeen','Armagh','Bangor','Bath','Belfast','Birmingham','Bradford',
    'Brighton and Hove','Bristol','Cambridge','Canterbury','Cardiff','Carlisle',
    'Chelmsford','Chester','Chichester','Coventry','Derby','Derry','Dundee',
    'Dunfermline','Durham','Edinburgh','Ely','Exeter','Glasgow','Gloucester',
    'Hereford','Inverness','Kingston upon Hull','Lancaster','Leeds','Leicester',
    'Lichfield','Lincoln','Lisburn','Liverpool','London','Manchester',
    'Newcastle upon Tyne','Newport','Newry','Norwich','Nottingham','Oxford',
    'Perth','Peterborough','Plymouth','Portsmouth','Preston','Ripon',
    'St Albans','St Asaph',"St David's",'Salford','Salisbury','Sheffield',
    'Southampton','Stirling','Stoke-on-Trent','Sunderland','Swansea','Truro',
    'Wakefield','Wells','Westminster','Winchester','Wolverhampton','Worcester',
    'York','Prefer not to say',
];

const NATIONALITIES = [
    'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda',
    'Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain',
    'Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan',
    'Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria',
    'Burkina Faso','Burundi','Cabo Verde','Cambodia','Cameroon','Canada',
    'Central African Republic','Chad','Chile','China','Colombia','Comoros',
    'Congo (Democratic Republic)','Congo (Republic)','Costa Rica',"Cote d'Ivoire",
    'Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica',
    'Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea',
    'Eritrea','Estonia','Eswatini','Ethiopia','Fiji','Finland','France','Gabon',
    'Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea',
    'Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India',
    'Indonesia','Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan',
    'Jordan','Kazakhstan','Kenya','Kiribati','Korea (North)','Korea (South)',
    'Kosovo','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia',
    'Libya','Liechtenstein','Lithuania','Luxembourg','Madagascar','Malawi',
    'Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania',
    'Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro',
    'Morocco','Mozambique','Myanmar (Burma)','Namibia','Nauru','Nepal',
    'Netherlands','New Zealand','Nicaragua','Niger','Nigeria','North Macedonia',
    'Norway','Oman','Pakistan','Palau','Panama','Papua New Guinea','Paraguay',
    'Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda',
    'Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines',
    'Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal',
    'Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia',
    'Solomon Islands','Somalia','South Africa','South Sudan','Spain','Sri Lanka',
    'Sudan','Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan',
    'Tanzania','Thailand','Timor-Leste','Togo','Tonga','Trinidad and Tobago',
    'Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine',
    'United Arab Emirates','United Kingdom','United States','Uruguay',
    'Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Yemen',
    'Zambia','Zimbabwe',
];

const SELECT_CLASS = 'w-full px-4 py-3 rounded-xl font-medium text-sm border-brutal bg-white focus:outline-none focus:ring-2 focus:ring-hp-accent';
const INPUT_CLASS  = 'w-full px-4 py-3 rounded-xl font-medium text-sm border-brutal bg-white focus:outline-none focus:ring-2 focus:ring-hp-accent';
const LABEL_CLASS  = 'block text-xs font-black uppercase tracking-widest mb-1.5';

export default function ExperiencedTalentForm() {
    const [firstName,    setFirstName]    = useState('');
    const [email,        setEmail]        = useState('');
    const [city,         setCity]         = useState('');
    const [otherCity,    setOtherCity]    = useState('');
    const [yearsExp,     setYearsExp]     = useState('');
    const [ethnicity,    setEthnicity]    = useState('');
    const [nationality,  setNationality]  = useState('');
    const [workRights,   setWorkRights]   = useState('');
    const [gender,       setGender]       = useState('');
    const [cvFile,       setCvFile]       = useState<File | null>(null);
    const [submitting,   setSubmitting]   = useState(false);
    const [submitted,    setSubmitted]    = useState(false);
    const [error,        setError]        = useState('');
    const fileRef = useRef<HTMLInputElement>(null);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setSubmitting(true);
        try {
            const fd = new FormData();
            fd.append('first-name',       firstName);
            fd.append('your-email',       email);
            fd.append('nearest-city',     city === 'Other' ? otherCity : city);
            fd.append('selected-other',   city === 'Other' ? otherCity : '');
            fd.append('years-experience', yearsExp);
            fd.append('ethnic-background',ethnicity);
            fd.append('your-nationality', nationality);
            fd.append('work-rights',      workRights);
            fd.append('applicant-gender', gender);
            if (cvFile) fd.append('your-file', cvFile);

            const res  = await fetch('/api/programmes/experienced-talent', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'mail_sent') {
                setSubmitted(true);
            } else {
                setError(data.message || data.invalid_fields?.[0]?.message || 'Something went wrong. Please try again.');
            }
        } catch {
            setError('Could not reach the server. Please try again.');
        } finally {
            setSubmitting(false);
        }
    }

    if (submitted) {
        return (
            <div className="p-8 rounded-2xl border-brutal shadow-brutal bg-white text-center space-y-4">
                <div className="w-16 h-16 rounded-full border-brutal shadow-brutal-sm bg-hp-accent text-white flex items-center justify-center text-2xl mx-auto">✓</div>
                <h3 className="font-black uppercase text-xl tracking-tight">Application Received</h3>
                <p className="text-text-2 max-w-sm mx-auto">
                    Thank you for applying to the Experienced Talent Programme. Our team will review your application and be in touch shortly.
                </p>
            </div>
        );
    }

    return (
        <form onSubmit={handleSubmit} className="space-y-5">
            {error && (
                <div className="p-4 rounded-xl border-2 border-red-400 bg-red-50 text-red-700 text-sm font-medium">
                    {error}
                </div>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label className={LABEL_CLASS}>First Name *</label>
                    <input
                        type="text"
                        required
                        value={firstName}
                        onChange={e => setFirstName(e.target.value)}
                        placeholder="Your first name"
                        className={INPUT_CLASS}
                    />
                </div>
                <div>
                    <label className={LABEL_CLASS}>Email Address *</label>
                    <input
                        type="email"
                        required
                        value={email}
                        onChange={e => setEmail(e.target.value)}
                        placeholder="your@email.com"
                        className={INPUT_CLASS}
                    />
                </div>
            </div>

            <div>
                <label className={LABEL_CLASS}>Nearest City / Location *</label>
                <select required value={city} onChange={e => setCity(e.target.value)} className={SELECT_CLASS}>
                    <option value="">— Please choose an option —</option>
                    {UK_CITIES.map(c => <option key={c} value={c}>{c}</option>)}
                    <option value="Other">Other (please specify)</option>
                </select>
                {city === 'Other' && (
                    <input
                        type="text"
                        required
                        value={otherCity}
                        onChange={e => setOtherCity(e.target.value)}
                        placeholder="Enter your city"
                        className={`${INPUT_CLASS} mt-2`}
                    />
                )}
            </div>

            <div>
                <label className={LABEL_CLASS}>Years of Working Experience *</label>
                <input
                    type="text"
                    required
                    value={yearsExp}
                    onChange={e => setYearsExp(e.target.value)}
                    placeholder="e.g. 5"
                    className={INPUT_CLASS}
                />
                <p className="text-xs text-text-3 mt-1">International experience counts.</p>
            </div>

            <div>
                <label className={LABEL_CLASS}>I am... *</label>
                <select required value={ethnicity} onChange={e => setEthnicity(e.target.value)} className={SELECT_CLASS}>
                    <option value="">— Please choose an option —</option>
                    <option value="African">African</option>
                    <option value="Caribbean">Caribbean</option>
                    <option value="Black British">Black British</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label className={LABEL_CLASS}>Nationality *</label>
                <select required value={nationality} onChange={e => setNationality(e.target.value)} className={SELECT_CLASS}>
                    <option value="">— Please choose an option —</option>
                    {NATIONALITIES.map(n => <option key={n} value={n}>{n}</option>)}
                </select>
            </div>

            <div>
                <label className={LABEL_CLASS}>Do you have the right to work full time in the UK? *</label>
                <div className="flex gap-4 mt-1">
                    {['Yes', 'No'].map(v => (
                        <label key={v} className="flex items-center gap-2 cursor-pointer font-semibold text-sm">
                            <input
                                type="radio"
                                name="work-rights"
                                value={v}
                                required
                                checked={workRights === v}
                                onChange={() => setWorkRights(v)}
                                className="w-4 h-4 accent-hp-accent"
                            />
                            {v}
                        </label>
                    ))}
                </div>
            </div>

            <div>
                <label className={LABEL_CLASS}>Gender *</label>
                <select required value={gender} onChange={e => setGender(e.target.value)} className={SELECT_CLASS}>
                    <option value="">— Please choose an option —</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label className={LABEL_CLASS}>Upload Your CV *</label>
                <div
                    className="w-full px-4 py-6 rounded-xl border-brutal bg-white text-center cursor-pointer hover:shadow-brutal transition-shadow"
                    onClick={() => fileRef.current?.click()}
                    onDragOver={e => e.preventDefault()}
                    onDrop={e => {
                        e.preventDefault();
                        const f = e.dataTransfer.files[0];
                        if (f) setCvFile(f);
                    }}
                >
                    <input
                        ref={fileRef}
                        type="file"
                        accept=".pdf,.doc,.docx"
                        className="hidden"
                        onChange={e => setCvFile(e.target.files?.[0] || null)}
                    />
                    {cvFile ? (
                        <div className="flex items-center justify-center gap-2">
                            <span className="font-black text-sm">📄 {cvFile.name}</span>
                            <button
                                type="button"
                                onClick={ev => { ev.stopPropagation(); setCvFile(null); if (fileRef.current) fileRef.current.value = ''; }}
                                className="text-red-500 text-xs font-bold hover:underline"
                            >
                                Remove
                            </button>
                        </div>
                    ) : (
                        <>
                            <p className="font-black text-sm mb-1">Click to upload or drag &amp; drop</p>
                            <p className="text-xs text-text-3">PDF, DOC, DOCX — can be a draft</p>
                        </>
                    )}
                </div>
            </div>

            <button
                type="submit"
                disabled={submitting}
                className="w-full bg-hp-dark text-white py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm disabled:opacity-60"
            >
                {submitting ? 'Submitting…' : 'Submit Application'}
            </button>
        </form>
    );
}
