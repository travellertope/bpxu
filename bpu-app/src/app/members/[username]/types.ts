export interface PublicWorkExperience {
  title: string;
  company: string;
  start_date?: string;
  end_date?: string;
  is_current?: boolean;
  description?: string;
}

export interface PublicEducation {
  institution: string;
  degree?: string;
  field_of_study?: string;
  start_year?: string;
  end_year?: string;
}

export interface PublicCertification {
  name: string;
  issuer?: string;
  year?: string;
}

export interface PublicTestimonial {
  quote: string;
  author: string;
  role: string;
}

export interface PublicMemberProfile {
  id: number;
  username: string;
  display_name: string;
  photo_url?: string;
  /* Passport fields */
  job_title?: string;
  current_employer?: string;
  profile_headline?: string;
  linkedin_url?: string;
  open_to_opportunities?: string;
  /* Location */
  location_city?: string;
  where_in_the_uk?: string;
  country_location?: string;
  /* Career */
  industry?: string;
  industryfield_of_expertise?: string;
  years_of_experience?: string;
  skills_separate?: string;
  level_of_education?: string;
  /* Bio */
  user_bio?: string;
  /* Membership */
  is_pro?: boolean;
  roles?: string[];
  member_since?: string;
  /* CV-parsed */
  experiences?: PublicWorkExperience[];
  educations?: PublicEducation[];
  certifications?: PublicCertification[];
  /* Status */
  placement_scheme?: string;
  mentorship_status?: string;
  /* Paired */
  is_paired_mentor?: boolean;
  /* Endorsements */
  endorsements?: { skill: string; count: number }[];
  total_endorsements?: number;
  /* Social */
  testimonials?: PublicTestimonial[];
}
