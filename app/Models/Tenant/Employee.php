<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Employee extends Model
{
    use BelongsToTenant, SoftDeletes;
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'employee_number',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'alternative_phone',
        'gender',
        'date_of_birth',
        'national_id',
        'kra_pin',
        'nhif_number',
        'nssf_number',
        'nationality',
        'address',
        'photo',
        'department',
        'job_title',
        'job_grade',
        'employment_type',
        'employment_status',
        'hire_date',
        'confirmation_date',
        'contract_end_date',
        'reporting_to',
        'work_location',
        'basic_salary',
        'professional_cadre',
        'registration_body',
        'registration_number',
        'license_expiry_date',
        'license_status',
        'indemnity_provider',
        'indemnity_expiry_date',
        'specialty',
        'cpd_points_required',
        'cpd_points_earned',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'bank_code',
    ];
    protected $casts = [
        'date_of_birth'         => 'date',
        'hire_date'             => 'date',
        'confirmation_date'     => 'date',
        'contract_end_date'     => 'date',
        'license_expiry_date'   => 'date',
        'indemnity_expiry_date' => 'date',
        'basic_salary'          => 'decimal:2',
    ];
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
    public function getLicenseStatusColorAttribute(): string
    {
        return match($this->license_status) {
            'valid'    => 'emerald',
            'expiring' => 'amber',
            'expired'  => 'red',
            default    => 'gray',
        };
    }
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }
    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }
    public function licenses()
    {
        return $this->hasMany(ProfessionalLicense::class);
    }
    public function disciplinaryCases()
    {
        return $this->hasMany(DisciplinaryCase::class);
    }
    public function grievances()
    {
        return $this->hasMany(Grievance::class);
    }
    public function trainingEnrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }
}
