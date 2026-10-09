<?php
/**
 * Global Markets section — "From India to the World"
 */

$eyebrow  = 'From India to the world';
$heading  = ['Coir Crafted in India.', 'Ready for Global Markets.'];
$para_one = 'Based in Namakkal, Tamil Nadu, India, <strong>FEEBLE EXPORTS</strong> is built with a global vision. We aim to connect India\'s natural coir resources and craftsmanship with customers and markets around the world.';
$para_two = 'With product sizing and specifications that can be adapted to different market requirements, we are working towards building reliable international partnerships and expanding our global network.';

// Where the routes start
$origin = ['name' => 'Namakkal, India', 'lat' => 11.22, 'lon' => 78.17];

// Market groups: each group is one legend item + its routes on the map
$markets = [
  ['id' => 'india', 'label' => 'India', 'flag' => 'in', 'cities' => [
      ['name' => 'Delhi',  'lat' => 28.61, 'lon' => 77.21],
      ['name' => 'Mumbai', 'lat' => 19.08, 'lon' => 72.88],
  ]],
  ['id' => 'uk-eu', 'label' => 'UK & Europe', 'flag' => 'uk', 'cities' => [
      ['name' => 'London',  'lat' => 51.51, 'lon' => -0.13],
      ['name' => 'Hamburg', 'lat' => 53.55, 'lon' => 9.99],
  ]],
  ['id' => 'us-ca', 'label' => 'USA & Canada', 'flag' => 'us', 'cities' => [
      ['name' => 'New York',  'lat' => 40.71, 'lon' => -74.01],
      ['name' => 'Vancouver', 'lat' => 49.28, 'lon' => -123.12],
  ]],
  ['id' => 'world', 'label' => 'Other Global Markets', 'flag' => 'globe', 'cities' => [
      ['name' => 'Dubai',        'lat' => 25.20,  'lon' => 55.27],
      ['name' => 'Johannesburg', 'lat' => -26.20, 'lon' => 28.05],
      ['name' => 'Sydney',       'lat' => -33.87, 'lon' => 151.21],
  ]],
];

$flags = [
  'in' => '<svg viewBox="0 0 60 40" aria-hidden="true"><rect width="60" height="13.4" fill="#ff9933"/><rect y="13.3" width="60" height="13.4" fill="#fff"/><rect y="26.6" width="60" height="13.4" fill="#138808"/><circle cx="30" cy="20" r="5" fill="none" stroke="#000080" stroke-width="1.2"/><circle cx="30" cy="20" r="1" fill="#000080"/></svg>',
  'uk' => '<svg viewBox="0 0 60 40" aria-hidden="true"><rect width="60" height="40" fill="#1f3a7a"/><path d="M0 0L60 40M60 0L0 40" stroke="#fff" stroke-width="8"/><path d="M0 0L60 40M60 0L0 40" stroke="#c8102e" stroke-width="2.6"/><path d="M30 0V40M0 20H60" stroke="#fff" stroke-width="13"/><path d="M30 0V40M0 20H60" stroke="#c8102e" stroke-width="7.5"/></svg>',
  'us' => '<svg viewBox="0 0 60 40" aria-hidden="true"><rect width="60" height="40" fill="#fff"/><g fill="#b22234"><rect width="60" height="3.1"/><rect y="6.2" width="60" height="3.1"/><rect y="12.3" width="60" height="3.1"/><rect y="18.5" width="60" height="3.1"/><rect y="24.6" width="60" height="3.1"/><rect y="30.8" width="60" height="3.1"/><rect y="36.9" width="60" height="3.1"/></g><rect width="26" height="21.5" fill="#3c3b6e"/><g fill="#fff"><circle cx="5" cy="4.5" r="1"/><circle cx="11" cy="4.5" r="1"/><circle cx="17" cy="4.5" r="1"/><circle cx="8" cy="10" r="1"/><circle cx="14" cy="10" r="1"/><circle cx="20" cy="10" r="1"/><circle cx="5" cy="15.5" r="1"/><circle cx="11" cy="15.5" r="1"/><circle cx="17" cy="15.5" r="1"/></g></svg>',
  'globe' => '<svg viewBox="0 0 40 40" aria-hidden="true"><g fill="none" stroke="#3a2a1d" stroke-width="2"><circle cx="20" cy="20" r="15"/><ellipse cx="20" cy="20" rx="6.5" ry="15"/><path d="M5 20h30M8 11.5h24M8 28.5h24"/></g></svg>',
];
?>
<section class="fx-markets" id="global-markets" aria-labelledby="fx-markets-title">
  <div class="fx-markets__inner">

    <div class="fx-markets__copy">
      <p class="fx-markets__eyebrow"><?= htmlspecialchars($eyebrow) ?></p>
      <h2 class="fx-markets__title" id="fx-markets-title">
        <?= htmlspecialchars($heading[0]) ?><br><?= htmlspecialchars($heading[1]) ?>
      </h2>
      <p><?= $para_one ?></p>
      <p><?= htmlspecialchars($para_two) ?></p>
    </div>

    <div class="fx-markets__visual"
         data-fx-map
         data-origin='<?= htmlspecialchars(json_encode($origin), ENT_QUOTES) ?>'
         data-markets='<?= htmlspecialchars(json_encode($markets), ENT_QUOTES) ?>'>

      <div class="fx-map" role="img"
           aria-label="World map showing export routes from Namakkal, India to India, UK and Europe, USA and Canada, and other global markets">
        <!-- JS draws the dotted map, routes and markers here -->
      </div>

      <ul class="fx-legend">
        <?php foreach ($markets as $m): ?>
          <li>
            <button type="button" class="fx-legend__item" data-group="<?= htmlspecialchars($m['id']) ?>">
              <span class="fx-legend__icon"><?= $flags[$m['flag']] ?></span>
              <span class="fx-legend__label"><?= htmlspecialchars($m['label']) ?></span>
            </button>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

  </div>
</section>
