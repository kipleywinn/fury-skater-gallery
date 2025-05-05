document.addEventListener('DOMContentLoaded', function () {
  const teamSelector = 'input[name="tax_input[team][]"]';
  const moonSelector = 'input[name="tax_input[moonlighting_team][]"]';

  function updateCheckboxStates() {
  const teamCheckboxes = document.querySelectorAll('#teamchecklist input[type="checkbox"]');
  const moonCheckboxes = document.querySelectorAll('#moonlighting_teamchecklist input[type="checkbox"]');

  const getLabelText = (checkbox) => checkbox.closest('label').textContent.trim();

  const teamChecked = [...teamCheckboxes].find(cb => cb.checked);
  const moonChecked = [...moonCheckboxes].find(cb => cb.checked);

  // Enable everything by default
  teamCheckboxes.forEach(cb => cb.disabled = false);
  moonCheckboxes.forEach(cb => cb.disabled = false);

  if (teamChecked) {
    const labelText = getLabelText(teamChecked);

    teamCheckboxes.forEach(cb => {
      if (cb !== teamChecked) cb.disabled = true;
    });

    moonCheckboxes.forEach(cb => {
      if (getLabelText(cb) === labelText) cb.disabled = true;
    });
  }

  if (moonChecked) {
    const labelText = getLabelText(moonChecked);

    moonCheckboxes.forEach(cb => {
      if (cb !== moonChecked) cb.disabled = true;
    });

    teamCheckboxes.forEach(cb => {
      if (getLabelText(cb) === labelText) cb.disabled = true;
    });
  }
}


  document.addEventListener('change', function (e) {
    if (
      e.target.matches(teamSelector) ||
      e.target.matches(moonSelector)
    ) {
      updateCheckboxStates();
    }
  });

  // Run on load in case of saved selections
  updateCheckboxStates();
});