document.addEventListener('DOMContentLoaded', () => {
  const regenerateButton = document.getElementById('regenerate-bio-popup');

  regenerateButton?.addEventListener('click', (event) => {
    event.preventDefault();

    const getInputVal = (name) => document.querySelector(`[data-name="${name}"] input`)?.value || '';

    const skaterName = document.getElementById('title')?.value.trim() || '';
    const nickname = getInputVal('skater_nickname');
    const pronounInput = getInputVal('skater_pronouns').toLowerCase();

    const checkedTeams = document.querySelectorAll('#teamchecklist input[type="checkbox"]:checked');

    let selectedTeams = Array.from(checkedTeams).map(input => {
      return input.closest('label').textContent.trim();
  });
    const team = selectedTeams[0] || 'No team selected';
    console.log(team);

    // Define pronoun grammar variations including verb agreement
    const pronouns = {
      he: { subject: 'He', object: 'him', possessive: 'his', has: 'has' },
      she: { subject: 'She', object: 'her', possessive: 'her', has: 'has' },
      they: { subject: 'They', object: 'them', possessive: 'their', has: 'have' }
  };

const p = pronouns[pronounInput] || pronouns.they; // fallback to they/them

const positions = getInputVal('skater_positions');
const otherPosition = getInputVal('skater_other_position');

let positionsArray = positions
.split(',')
.map(pos => pos.trim())
.filter(pos => pos.toLowerCase() !== 'other');

// Add the other position if it exists
if (otherPosition && otherPosition.trim() !== '') {
  positionsArray.push(otherPosition.trim());
}

const combinedPositions = positionsArray.join(', ');


const yearStarted = getInputVal('skater_year_started_playing');
const derbySpouse = getInputVal('skater_derby_spouse');
const fav1 = getInputVal('skater_favorite_thing_1');
const fav2 = getInputVal('skater_favorite_thing_2');
const fav3 = getInputVal('skater_favorite_thing_3');

const skaterNumber = document.getElementById('skater_number').value || '';
const headshot = document.querySelector('#set-post-thumbnail img')?.src || '';
const secondaryHeadshot = document.querySelector('[data-name="skater_secondary_headshot"] img').checkVisibility() ? document.querySelector('[data-name="skater_secondary_headshot"] img')?.src.replace(/-150x150/g, '') : headshot;
const teamLogoUrl = document.getElementById('current-skater-team')?.dataset.logoUrl || '';

const careerInfo = getInputVal('skater_career');

const optionalQuestions = [
  { label: "Do you wear socks when you skate?", field: 'do_you_wear_socks_when_you_skate' },
  { label: "How do you waste time most often?", field: 'how_do_you_waste_time_most_often' },
  { label: "Do you have any other hobbies outside of skating?", field: 'do_you_have_any_other_hobbies_outside_of_skating' },
  { label: "What is your go-to karaoke song?", field: 'what_is_your_go-to_karaoke_song' },
  { label: "What emojis do you use the most often?", field: 'what_emojis_do_you_use_the_most_often' },
  { label: "Did you pick your derby name or did your derby name pick you?", field: 'did_you_pick_your_derby_name_or_did_your_derby_name_pick_you' },
  { label: "What is your most useless talent?", field: 'what_is_your_most_useless_talent' },
  { label: "Is a hot dog a sandwich?", field: 'is_a_hot_dog_a_sandwich' },
  { label: "Would you rather fight 100 duck-sized horses or 1 horse-sized duck?", field: 'would_you_rather_fight_100_duck-sized_horses_or_1_horse_sized_duck' },
  { label: "When did you attend your first concert and who did you see?", field: 'when_did_you_attend_your_first_concert_and_who_did_you_see' },
  { label: "If your pet could talk, what do you think they might say about you?", field: 'if_your_pet_could_talk_what_do_you_think_they_might_say_about_you' },
  { label: "Do you collect anything?", field: 'do_you_collect_anything' },
  { label: "What are your top 3 skating injuries?", field: 'what_are_your_top_3_skating_injuries' },
  { label: "What is the most ridiculous nightmare you've ever had?", field: 'what_is_the_most_ridiculous_nightmare_youve_ever_had' }
      // Note: “Special notes?” is excluded here intentionally
];

    // Generate Q&A block
let dynamicContent = '';
optionalQuestions.forEach(({ label, field }) => {
  const answer = getInputVal(field);
  if (answer) {
    dynamicContent += `<p><strong>${label}</strong></p>\n<blockquote><p>${answer}</p></blockquote>\n`;
}
});

let template;
if (team !== "Crew") {
   template = `
<div class="skater-bio-popup">
  <div class="bio-title">
        <h1>${skaterName} ${skaterNumber ? `#${skaterNumber}` : ''}</h1>
        ${nickname ? `<h5>AKA: ${nickname}</h5>` : ''}
    <p><img src="${teamLogoUrl}"></p>
    <h4>${combinedPositions}</h4>
  </div>
  <div class="mainbody">
    <img src="${secondaryHeadshot}" alt="${skaterName}" class="skaterPic alignnone size-small">
        <p>${nickname ? nickname : skaterName} has been playing roller derby since ${yearStarted || 'N/A'}${derbySpouse ? ` and ${p.possessive} derby spouse is ${derbySpouse}.` : '.'}</p>

    <p>A few of ${p.possessive} favorite things:</p>
    <ul>
        ${fav1 ? `<li>${fav1}</li>` : ''}
        ${fav2 ? `<li>${fav2}</li>` : ''}
        ${fav3 ? `<li>${fav3}</li>` : ''}
    </ul>
    ${careerInfo ? `<p><strong>Some cool shit about ${nickname ? nickname : skaterName}:</strong></p><blockquote>${careerInfo}</blockquote>` : ''}
    ${dynamicContent}
  </div>
        </div>`
    } else {
        template = `<div class="skater-bio-popup">
            <div class="bio-title">
                <h1>${skaterName}</h1>
                <!--h4>You can put a subtitle here</h4-->
            </div>
            <div class="mainbody center">
                <img src="${headshot}" alt="${skaterName}" class="crewPic alignnone size-small">
            </div>
        </div>`
    };

    const textarea = document.querySelector('[data-name="skater_popup_html"] textarea');
    if (textarea) textarea.value = template.trim();
});
});
