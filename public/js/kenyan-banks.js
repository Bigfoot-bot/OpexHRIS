const kenyanBanks = {
    "KCB Bank": ["Nairobi Main", "Westlands", "Moi Avenue", "Upperhill", "Karen", "Gigiri", "Kilimani", "Thika Road", "Mombasa Main", "Kisumu Main", "Nakuru Main", "Eldoret Main", "Nyeri", "Meru", "Kitale", "Kakamega", "Garissa", "Machakos", "Bungoma", "Kericho"],
    "Equity Bank": ["Nairobi Main", "Westlands", "Queensway", "Moi Avenue", "Karen", "Eastleigh", "Thika", "Mombasa", "Kisumu", "Nakuru", "Eldoret", "Nyeri", "Meru", "Kitale", "Bungoma", "Kakamega", "Garissa", "Machakos", "Kericho", "Embu"],
    "Co-operative Bank": ["Nairobi Main", "Westlands", "Moi Avenue", "Upperhill", "Karen", "Kilimani", "Thika", "Mombasa", "Kisumu", "Nakuru", "Eldoret", "Nyeri", "Meru", "Kitale", "Kakamega", "Garissa", "Machakos", "Bungoma", "Kericho", "Embu"],
    "NCBA Bank": ["Nairobi Main", "Westlands", "Upperhill", "Karen", "Mombasa", "Kisumu", "Nakuru", "Eldoret", "Thika", "Nyeri"],
    "Absa Bank": ["Nairobi Main", "Westlands", "Moi Avenue", "Upperhill", "Karen", "Mombasa", "Kisumu", "Nakuru", "Eldoret", "Nyeri", "Thika"],
    "Standard Chartered": ["Nairobi Main", "Westlands", "Upperhill", "Karen", "Mombasa", "Kisumu", "Nakuru", "Eldoret"],
    "DTB Bank": ["Nairobi Main", "Westlands", "Upperhill", "Mombasa", "Kisumu", "Nakuru", "Eldoret", "Thika"],
    "I&M Bank": ["Nairobi Main", "Westlands", "Upperhill", "Karen", "Mombasa", "Kisumu", "Nakuru", "Thika"],
    "Family Bank": ["Nairobi Main", "Westlands", "Thika", "Mombasa", "Kisumu", "Nakuru", "Eldoret", "Nyeri", "Meru", "Kitale", "Bungoma", "Kakamega", "Machakos", "Kericho", "Embu"],
    "Stanbic Bank": ["Nairobi Main", "Westlands", "Upperhill", "Mombasa", "Kisumu", "Nakuru"],
    "Prime Bank": ["Nairobi Main", "Westlands", "Upperhill", "Mombasa", "Kisumu"],
    "HFC Bank": ["Nairobi Main", "Westlands", "Mombasa", "Kisumu", "Nakuru"],
    "Gulf African Bank": ["Nairobi Main", "Westlands", "Mombasa", "Kisumu", "Eastleigh"],
    "First Community Bank": ["Nairobi Main", "Westlands", "Mombasa", "Kisumu", "Eastleigh", "Garissa"],
    "SBM Bank": ["Nairobi Main", "Westlands", "Mombasa", "Kisumu"],
    "UBA Kenya": ["Nairobi Main", "Westlands", "Mombasa"],
    "Paramount Bank": ["Nairobi Main", "Westlands", "Mombasa"],
    "Guardian Bank": ["Nairobi Main", "Westlands"],
    "Victoria Commercial Bank": ["Nairobi Main", "Westlands"],
    "Consolidated Bank": ["Nairobi Main", "Mombasa", "Kisumu", "Nakuru", "Eldoret"],
    "Development Bank of Kenya": ["Nairobi Main"],
    "Housing Finance": ["Nairobi Main", "Westlands", "Mombasa", "Kisumu", "Nakuru"],
    "Sidian Bank": ["Nairobi Main", "Westlands", "Mombasa", "Kisumu", "Nakuru", "Meru"],
    "Spire Bank": ["Nairobi Main", "Westlands"],
    "M-Pesa": ["Mobile Money"],
    "Other": []
};

const bankCodes = {
    "KCB Bank": "01",
    "Equity Bank": "68",
    "Co-operative Bank": "11",
    "NCBA Bank": "07",
    "Absa Bank": "03",
    "Standard Chartered": "02",
    "DTB Bank": "63",
    "I&M Bank": "57",
    "Family Bank": "70",
    "Stanbic Bank": "31",
    "Prime Bank": "10",
    "HFC Bank": "61",
    "Gulf African Bank": "72",
    "First Community Bank": "74",
    "SBM Bank": "66",
    "UBA Kenya": "76",
    "Paramount Bank": "50",
    "Guardian Bank": "55",
    "Victoria Commercial Bank": "54",
    "Consolidated Bank": "23",
    "Development Bank of Kenya": "39",
    "Housing Finance": "61",
    "Sidian Bank": "18",
    "Spire Bank": "67",
    "M-Pesa": "63",
    "Other": ""
};

function initBankFields(bankSelectId, branchSelectId, branchInputId, bankCodeId) {
    const bankSelect   = document.getElementById(bankSelectId);
    const branchSelect = document.getElementById(branchSelectId);
    const branchInput  = document.getElementById(branchInputId);
    const bankCodeInput = document.getElementById(bankCodeId);

    if (!bankSelect) return;

    // Populate bank dropdown
    const currentBank = bankSelect.dataset.current || '';
    Object.keys(kenyanBanks).forEach(bank => {
        const option = document.createElement('option');
        option.value = bank;
        option.textContent = bank;
        if (bank === currentBank) option.selected = true;
        bankSelect.appendChild(option);
    });

    function updateBranches() {
        const selectedBank = bankSelect.value;
        const branches = kenyanBanks[selectedBank] || [];
        const currentBranch = branchSelect.dataset.current || '';

        // Auto-fill bank code
        if (bankCodeInput && bankCodes[selectedBank]) {
            bankCodeInput.value = bankCodes[selectedBank];
        }

        // Clear and repopulate branch dropdown
        branchSelect.innerHTML = '<option value="">Select Branch</option>';
        branches.forEach(branch => {
            const option = document.createElement('option');
            option.value = branch;
            option.textContent = branch;
            if (branch === currentBranch) option.selected = true;
            branchSelect.appendChild(option);
        });

        // Add "Other (type manually)" option
        const otherOption = document.createElement('option');
        otherOption.value = '__other__';
        otherOption.textContent = 'Other (type manually)';
        branchSelect.appendChild(otherOption);

        // Show/hide manual input
        updateBranchInput();
    }

    function updateBranchInput() {
        if (branchSelect.value === '__other__') {
            branchInput.style.display = 'block';
            branchInput.required = true;
        } else {
            branchInput.style.display = 'none';
            branchInput.required = false;
            branchInput.value = '';
        }
    }

    bankSelect.addEventListener('change', updateBranches);
    branchSelect.addEventListener('change', updateBranchInput);

    // Initialize on page load
    if (currentBank) updateBranches();
}
