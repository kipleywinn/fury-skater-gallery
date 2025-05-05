# Fury Skater Gallery

**Fury Skater Gallery** is a custom WordPress plugin designed for roller derby leagues to showcase skaters and crew members in a team-based, filterable gallery. It features modal popups with bios, custom post types, taxonomy filtering, and dynamic styling based on team colors.

Originally developed for [Rockstar Roller Derby](https://rockstarrollerderby.com) by skater Bi-Furious (Kipley Winn).

## Highlighted Features

- Custom Post Type: Skater
- Custom Taxonomies: Team & Moonlighting Team
- ACF Fields for bios, pronouns, positions, and more
- Filterable gallery by team
- Modal popups with detailed skater bios and images
- Pronoun-aware grammar in bios (he/him, she/her, they/them)
- Crew profiles with simplified modal view
- Custom admin overview screen that groups skaters by team and moonlighting status
- Mark skaters with bouting status to generate team rosters separate from all skaters assigned to that team in general

## Installation

1. Upload the plugin folder to your WordPress installation under wp-content/plugins/
2. Ensure the [Advanced Custom Fields (ACF) plugin](https://advancedcustomfields.com) is installed and activated.
3. Activate the plugin in the WordPress admin under Plugins > Installed Plugins.

## Usage

1. Add Teams to the custom taxonomy
  - Teams are automatically synced with Moonlighting Teams
2. Go to Skaters > Add New to create a new skater or crew profile
3. Assign the profile to one or more Teams using the taxonomy box
4. Fill out fields for pronouns, bio content, position, special roles, and moonlighting status
5. Generate the modal content and proofread, making any necessary changes
6. Use the `[fury-skater-gallery]` shortcode on any page or post to embed the gallery

## Suggested Integrations

- Use [Gravity Forms](https://www.gravityforms.com) with the [Advanced Post Creation](https://www.gravityforms.com/add-ons/advanced-post-creation/) add-on to capture skater bio information and generate a Skater profile for each entry

## Example:

`[fury-skater-gallery]`

Skater thumbnails will appear in a responsive grid with team-colored borders. Clicking a skater opens a modal popup with their full bio.

Crew members will display in the same gallery but use a simplified popup layout with just an image and caption.

## Admin Features

- Admin overview page groups skaters by team
- Moonlighting skaters are listed separately after their primary team
- Quick Edit and Bulk Edit support for bouting_status, moonlighting_status, Team, & Moonlighting Team

## Customization

- Team border colors automatically applied based on the Team color set in the Teams taxonomy
- Modal popups are generated in each skater profile dynamically using JavaScript but the code is completely customizable after generation
- Settings page to select main color options for the modal

## License

This plugin is open source and distributed under the GPLV3 License.

Developed with ❤️ for the roller derby community.

