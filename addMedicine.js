let medicineList = [];

function addMedicine() {
    const medicineName = document.getElementById('medicine_name').value;
    const dose = document.getElementById('dose').value;
    const time = document.getElementById('time').value;
    const duration = document.getElementById('duration').value;
    const notes = document.getElementById('notes').value;

    if (medicineName && dose && time && duration) {
        medicineList.push({ medicineName, dose, time, duration, notes });
        clearForm();
    } else {
        alert('لطفاً همه فیلدهای ضروری را پر کنید.');
    }
}

function clearForm() {
    document.getElementById('medicine_name').value = '';
    document.getElementById('dose').value = '';
    document.getElementById('time').value = '';
    document.getElementById('duration').value = '';
    document.getElementById('notes').value = '';
}

document.getElementById('medicineForm').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('اطلاعات شما با موفقیت ثبت شد.');
});
