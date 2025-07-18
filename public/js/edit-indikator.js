document.addEventListener('DOMContentLoaded', function () {
    const tipeJawabanSelect = document.getElementById('tipe_jawaban');
    const opsiAbcdeDiv = document.getElementById('opsi_abcde');
    const tambahOpsiBtn = document.getElementById('btn_tambah_opsi');
    const tambahOpsiWrapper = document.getElementById('tambah_opsi_wrapper');
    const areaSelect = document.getElementById('area_id');
    const subAreaSelect = document.getElementById('sub_area_id');
    const totalBobotInput = document.getElementById('bobot_total');

    const abjad = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    const formElement = document.querySelector('form');
    const subAreaIdLama = parseInt(formElement?.dataset.subareaId || 0);

    function getLastOpsiIndex() {
        const inputs = document.querySelectorAll('[name^="opsi["][name$="[opsi]"]');
        let lastIndex = -1;
        inputs.forEach(input => {
            const huruf = input.value.toUpperCase();
            const index = abjad.indexOf(huruf);
            if (index > lastIndex) lastIndex = index;
        });
        return lastIndex + 1;
    }

    let opsiCount = getLastOpsiIndex();

    function toggleOpsiAbcde() {
        const tipe = tipeJawabanSelect.value;
        const isAbcde = tipe === 'abcde';

        opsiAbcdeDiv.classList.toggle('hidden', !isAbcde);
        if (tambahOpsiWrapper) tambahOpsiWrapper.classList.toggle('hidden', !isAbcde);
    }

    function updateSubAreas() {
        const selectedOption = areaSelect.options[areaSelect.selectedIndex];
        const subAreas = JSON.parse(selectedOption.getAttribute('data-subareas') || '[]');

        subAreaSelect.innerHTML = '';
        subAreas.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.text = sub.name;
            if (parseInt(sub.id) === subAreaIdLama) option.selected = true;
            subAreaSelect.appendChild(option);
        });
    }

    if (tambahOpsiBtn) {
        tambahOpsiBtn.addEventListener('click', () => {
            if (opsiCount >= abjad.length) return;
            const huruf = abjad[opsiCount];

            const wrapper = document.createElement('div');
            wrapper.className = 'bg-gray-50 p-4 rounded shadow-sm';

            wrapper.innerHTML = `
                <label class="block font-medium">Opsi ${huruf}</label>
                <input type="text" name="opsi[${opsiCount}][teks]" class="w-full border rounded px-3 py-2 mb-1" required>

                <label class="block font-medium">Bobot</label>
                <input type="number" step="0.01" min="0" max="1" name="opsi[${opsiCount}][bobot]"
                    class="w-full border rounded px-3 py-2" required>

                <input type="hidden" name="opsi[${opsiCount}][opsi]" value="${huruf}">
            `;

            opsiAbcdeDiv.appendChild(wrapper);
            opsiCount++;
            hitungTotalBobot();
        });
    }

    function hitungTotalBobot() {
        const bobotInputs = document.querySelectorAll('[name^="opsi["][name$="[bobot]"]');
        let total = 0;
        bobotInputs.forEach(input => {
            const val = parseFloat(input.value.replace(',', '.'));
            if (!isNaN(val)) total += val;
        });
        if (totalBobotInput) {
            totalBobotInput.value = total.toFixed(2).replace('.', ',');
        }
    }

    document.addEventListener('input', function (e) {
        if (e.target.name?.includes('[bobot]')) {
            hitungTotalBobot();
        }
    });

    toggleOpsiAbcde();
    updateSubAreas();
    hitungTotalBobot();

    tipeJawabanSelect.addEventListener('change', toggleOpsiAbcde);
    areaSelect.addEventListener('change', updateSubAreas);
});
