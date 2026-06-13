'use client';

import React, { useState } from 'react';
import { BPUUser, ACFProfile, WorkExperience, Education, Certification } from '@/lib/auth';
import { BPUApi } from '@/lib/api';

interface Props {
  user: BPUUser;
}

export default function ProfileClient({ user }: Props) {
  const isPro = user.is_pro;

  const [profile, setProfile] = useState<ACFProfile>(user.profile);
  const [editForm, setEditForm] = useState<Partial<ACFProfile>>(profile);
  const [saving, setSaving] = useState(false);
  const [saveMsg, setSaveMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

  const [weeklyEmails, setWeeklyEmails] = useState(false);
  const [prefSaving, setPrefSaving] = useState(false);
  const [prefMsg, setPrefMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

  const [experiences] = useState<WorkExperience[]>(user.experiences || []);
  const [educations] = useState<Education[]>(user.educations || []);
  const [certifications] = useState<Certification[]>(user.certifications || []);
  const [cvLanguages] = useState<string>(user.languages || '');
  const [cvParsedAt] = useState<string>(user.cv_parsed_at || '');

  const handleProfileSave = async () => {
    setSaving(true);
    setSaveMsg(null);
    try {
      const res = await fetch('/api/member/profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(editForm),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Save failed.');
      setProfile({ ...profile, ...editForm });
      setSaveMsg({ type: 'ok', text: 'Profile saved successfully.' });
    } catch (err: unknown) {
      setSaveMsg({ type: 'err', text: err instanceof Error ? err.message : 'Could not save profile.' });
    } finally {
      setSaving(false);
    }
  };

  const handlePrefSave = async () => {
    setPrefSaving(true);
    const ok = await BPUApi.updatePreferences({ weekly_emails: weeklyEmails });
    setPrefMsg(ok
      ? { type: 'ok', text: 'Preferences saved.' }
      : { type: 'err', text: 'Could not save preferences.' }
    );
    setPrefSaving(false);
  };

  return (
    <div className="wrap-sm fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
      <div className="flex items-start justify-between gap-4">
        <div>
          <h2 className="text-xl font-bold">My Profile</h2>
          <p className="section-sub">This information is used for job matching and mentor pairing.</p>
        </div>
        <div className="flex items-center gap-2 shrink-0">
          <button
            onClick={handleProfileSave}
            disabled={saving}
            className="btn btn-amber btn-sm"
          >
            {saving ? 'Saving…' : 'Save changes'}
          </button>
        </div>
      </div>

      {saveMsg && (
        <div className={`alert ${saveMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
          {saveMsg.text}
        </div>
      )}

      <div className="card card-p space-y-5">
        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Account</p>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="field-label">Display name</label>
            <input className="field-input bg-bg" value={user.display_name} disabled readOnly />
          </div>
          <div>
            <label className="field-label">Email</label>
            <input className="field-input bg-bg" value={user.email} disabled readOnly />
          </div>
        </div>
      </div>

      <div className="card card-p space-y-5">
        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Personal details</p>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {([
            ['First name',  'first_name'],
            ['Last name',   'last_name'],
            ['Phone',       'phone_number'],
          ] as [string, keyof ACFProfile][]).map(([label, key]) => (
            <div key={key}>
              <label className="field-label">{label}</label>
              <input
                className="field-input"
                value={(editForm[key] as string) || ''}
                onChange={e => setEditForm(f => ({ ...f, [key]: e.target.value }))}
              />
            </div>
          ))}
          <div>
            <label className="field-label">Date of birth</label>
            <input
              type="date"
              className="field-input"
              value={(editForm.birthday as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, birthday: e.target.value }))}
            />
          </div>
          <div>
            <label className="field-label">Gender</label>
            <select
              className="field-input"
              value={(editForm.what_is_your_gender as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, what_is_your_gender: e.target.value }))}
            >
              <option value="">Select…</option>
              {['Male', 'Female', 'Prefer Not to Say', 'Others'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          <div>
            <label className="field-label">Sexuality</label>
            <select
              className="field-input"
              value={(editForm.your_sexuality as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, your_sexuality: e.target.value }))}
            >
              <option value="">Select…</option>
              {['Asexual', 'Bisexual', 'Gay', 'Intersex', 'Lesbian', 'Queer', 'Straight', 'Transgender', 'Prefer not to say'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
        </div>
      </div>

      <div className="card card-p space-y-5">
        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Location</p>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div className="sm:col-span-2">
            <label className="field-label">Country</label>
            <select
              className="field-input"
              value={(editForm.country_location as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, country_location: e.target.value }))}
            >
              <option value="">Select…</option>
              {[
                'United Kingdom',
                'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cabo Verde','Cambodia','Cameroon','Canada','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia','Fiji','Finland','France','Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','North Korea','North Macedonia','Norway','Oman','Pakistan','Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine','United Arab Emirates','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe',
              ].map(c => <option key={c} value={c}>{c}</option>)}
            </select>
          </div>
          {editForm.country_location === 'United Kingdom' && (
            <div>
              <label className="field-label">Where in the UK?</label>
              <select
                className="field-input"
                value={(editForm.where_in_the_uk as string) || ''}
                onChange={e => setEditForm(f => ({ ...f, where_in_the_uk: e.target.value }))}
              >
                <option value="">Select…</option>
                {['England', 'Scotland', 'Wales', 'Northern Ireland'].map(o => <option key={o} value={o}>{o}</option>)}
              </select>
            </div>
          )}
          <div>
            <label className="field-label">City</label>
            <input
              className="field-input"
              value={(editForm.location_city as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, location_city: e.target.value }))}
            />
          </div>
        </div>
      </div>

      <div className="card card-p space-y-5">
        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Background</p>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="field-label">Education level</label>
            <select
              className="field-input"
              value={(editForm.level_of_education as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, level_of_education: e.target.value }))}
            >
              <option value="">Select…</option>
              {["High School","Bachelor's Degree","Professional Qualification","Masters Degree","PhD","Other","Prefer Not to Answer"].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          <div>
            <label className="field-label">Ethnicity</label>
            <select
              className="field-input"
              value={(editForm.how_would_you_best_describe_your_ethnicity as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, how_would_you_best_describe_your_ethnicity: e.target.value }))}
            >
              <option value="">Select…</option>
              {['African','Asian','Black British','Black Caribbean','Gypsy or Irish Traveller','Hispanic','Mixed Ethnic Group','Other Black Background','White'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          <div>
            <label className="field-label">First-generation immigrant?</label>
            <select
              className="field-input"
              value={(editForm['first-generation_immigrant'] as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, 'first-generation_immigrant': e.target.value }))}
            >
              <option value="">Select…</option>
              <option value="Yes">Yes</option>
              <option value="No">No</option>
            </select>
          </div>
          <div>
            <label className="field-label">Disability</label>
            <select
              className="field-input"
              value={(editForm.do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of: e.target.value }))}
            >
              <option value="">Select…</option>
              {['No Disability','Cognitive or learning disability','Hearing impairment','Mobility impairment','Visual impairment','Others (Input below)'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          {editForm.do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of === 'Others (Input below)' && (
            <div className="sm:col-span-2">
              <label className="field-label">Please describe your disability</label>
              <input
                className="field-input"
                placeholder="Describe your disability…"
                value={(editForm.other_disability as string) || ''}
                onChange={e => setEditForm(f => ({ ...f, other_disability: e.target.value }))}
              />
            </div>
          )}
        </div>
      </div>

      <div className="card card-p space-y-5">
        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Career</p>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="field-label">Employment status</label>
            <select
              className="field-input"
              value={(editForm.current_employment_status as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, current_employment_status: e.target.value }))}
            >
              <option value="">Select…</option>
              {['Employed Full-Time','Employed Part-Time','Self-employed','Not employed but looking for work','Not employed and not looking for work','Retired','Student','Prefer Not to Answer'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          <div>
            <label className="field-label">Industry</label>
            <select
              className="field-input"
              value={(editForm.industry as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, industry: e.target.value }))}
            >
              <option value="">Select…</option>
              {['Accounting','Administration & Office Support','Advertising, Arts & Media','Banking & Financial Services','Call Centre & Customer Service','Community Services & Development','Construction','Consulting & Strategy','Education & Training','Engineering','Farming, Animals & Conservation','Government & Defence','Healthcare & Medical','Hospitality & Tourism','Human Resources & Recruitment','Information & Communication Technology','Insurance & Superannuation','Legal','Manufacturing, Transport & Logistics','Marketing & Communications','Mining, Resources & Energy','Real Estate & Property','Retail & Consumer Products','Sales','Science & Technology','Self Employment','Sport & Recreation','Trades & Services','Other'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          <div>
            <label className="field-label">Field of expertise</label>
            <select
              className="field-input"
              value={(editForm.industryfield_of_expertise as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, industryfield_of_expertise: e.target.value }))}
            >
              <option value="">Select…</option>
              {['Accounting & Finance','Administration','Arts & Design','Business Development','Consulting','Customer Service','Data & Analytics','Education','Engineering','Healthcare','HR & Recruitment','IT & Software','Law & Legal Services','Logistics & Supply Chain','Management','Marketing & Communications','Media & Journalism','Operations','Policy & Government','Project Management','Property & Real Estate','Research & Science','Sales','Social Work & Community','Sport & Fitness','Other'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          <div>
            <label className="field-label">Years of experience</label>
            <select
              className="field-input"
              value={(editForm.years_of_experience as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, years_of_experience: e.target.value }))}
            >
              <option value="">Select…</option>
              {[...Array.from({ length: 40 }, (_, i) => String(i + 1)), '40+'].map(o => <option key={o} value={o}>{o}</option>)}
            </select>
          </div>
          {editForm.industryfield_of_expertise === 'Other' && (
            <div className="sm:col-span-2">
              <label className="field-label">Please specify your field of expertise</label>
              <input
                className="field-input"
                placeholder="Your field…"
                value={(editForm.expertise_not_listed as string) || ''}
                onChange={e => setEditForm(f => ({ ...f, expertise_not_listed: e.target.value }))}
              />
            </div>
          )}
          <div className="sm:col-span-2">
            <label className="field-label">Skills (comma-separated)</label>
            <input
              className="field-input"
              placeholder="e.g. React, Product Strategy, SQL"
              value={(editForm.skills_separate as string) || ''}
              onChange={e => setEditForm(f => ({ ...f, skills_separate: e.target.value }))}
            />
          </div>
        </div>
      </div>

      <div className="card card-p space-y-4">
        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Bio</p>
        <div>
          <label className="field-label">Professional biography</label>
          <textarea
            className="field-input field-textarea"
            placeholder="Write a short professional bio…"
            value={(editForm.user_bio as string) || ''}
            onChange={e => setEditForm(f => ({ ...f, user_bio: e.target.value }))}
          />
        </div>
      </div>

      <div className="flex justify-end">
        <button
          onClick={handleProfileSave}
          disabled={saving}
          className="btn btn-amber"
        >
          {saving ? 'Saving…' : 'Save changes'}
        </button>
      </div>

      {/* CV-parsed structured data */}
      {(experiences.length > 0 || educations.length > 0 || certifications.length > 0 || cvLanguages) && (
        <>
          <div className="divider" />
          <div className="flex items-center justify-between gap-2">
            <p className="text-xs font-bold uppercase tracking-wide text-text-3">Parsed from your CV</p>
            {cvParsedAt && <p className="text-xs text-text-3">Last updated: {cvParsedAt}</p>}
          </div>
          <p className="text-xs text-text-2">This information was automatically extracted from your uploaded CV. Re-upload your CV to refresh it.</p>

          {experiences.length > 0 && (
            <div className="card card-p space-y-4">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Work Experience</p>
              <div className="space-y-4">
                {experiences.map((exp, i) => (
                  <div key={i} className="border-l-2 border-brand pl-4 space-y-1">
                    <p className="font-semibold text-sm">{exp.title}</p>
                    <p className="text-sm text-text-2">{exp.company}</p>
                    {(exp.start_date || exp.end_date) && (
                      <p className="text-xs text-text-3">
                        {exp.start_date || ''}{exp.start_date && (exp.end_date || exp.is_current) ? ' – ' : ''}{exp.is_current ? 'Present' : exp.end_date || ''}
                      </p>
                    )}
                    {exp.description && <p className="text-xs text-text-2 leading-relaxed">{exp.description}</p>}
                  </div>
                ))}
              </div>
            </div>
          )}

          {educations.length > 0 && (
            <div className="card card-p space-y-4">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Education</p>
              <div className="space-y-3">
                {educations.map((edu, i) => (
                  <div key={i} className="border-l-2 border-brand pl-4 space-y-1">
                    <p className="font-semibold text-sm">{edu.institution}</p>
                    {(edu.degree || edu.field_of_study) && (
                      <p className="text-sm text-text-2">{[edu.degree, edu.field_of_study].filter(Boolean).join(', ')}</p>
                    )}
                    {(edu.start_year || edu.end_year) && (
                      <p className="text-xs text-text-3">{edu.start_year || ''}{edu.start_year && edu.end_year ? ' – ' : ''}{edu.end_year || ''}</p>
                    )}
                  </div>
                ))}
              </div>
            </div>
          )}

          {certifications.length > 0 && (
            <div className="card card-p space-y-4">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Certifications</p>
              <ul className="space-y-2">
                {certifications.map((cert, i) => (
                  <li key={i} className="flex items-start gap-2 text-sm">
                    <span className="text-brand mt-0.5">{'✓'}</span>
                    <span>
                      <span className="font-medium">{cert.name}</span>
                      {cert.issuer && <span className="text-text-2"> &mdash; {cert.issuer}</span>}
                      {cert.year && <span className="text-text-3"> ({cert.year})</span>}
                    </span>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {cvLanguages && (
            <div className="card card-p space-y-3">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Languages</p>
              <p className="text-sm">{cvLanguages}</p>
            </div>
          )}
        </>
      )}

      {/* Email preferences -- Pro only */}
      <div className="divider" />
      <div className="card card-p space-y-4">
        <div className="flex items-center justify-between">
          <p className="text-xs font-bold uppercase tracking-wide text-text-3">Email Preferences</p>
          {!isPro && <span className="badge badge-amber">Pro</span>}
        </div>
        {isPro ? (
          <>
            <label className="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                checked={weeklyEmails}
                onChange={e => setWeeklyEmails(e.target.checked)}
                className="w-4 h-4"
              />
              <span className="text-sm">Weekly job digest &mdash; top matches every Monday</span>
            </label>
            {prefMsg && (
              <div className={`alert ${prefMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                {prefMsg.text}
              </div>
            )}
            <button onClick={handlePrefSave} disabled={prefSaving} className="btn btn-outline btn-sm">
              {prefSaving ? 'Saving…' : 'Save preferences'}
            </button>
          </>
        ) : (
          <div className="flex items-center justify-between gap-4">
            <p className="text-sm text-text-2">Weekly job digest and notification controls are available with Pro.</p>
            <a href="/upgrade" className="btn btn-amber btn-sm shrink-0">Upgrade &rarr;</a>
          </div>
        )}
      </div>
    </div>
  );
}
