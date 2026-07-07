<?php
/**
 * Become a Host FAQ.
 *
 * @package Torby
 */

$faqs = array(
    array(
        'question' => __( 'How does Tourbi pricing work?', 'torby' ),
        'answer'   => __( 'Tourbi uses the $18 per bike per hour support rate plus a 5% service fee to estimate Host earnings. Final Host payouts are reviewed and handled manually by Tourbi.', 'torby' ),
    ),
    array(
        'question' => __( 'Can I save an Experience before it is complete?', 'torby' ),
        'answer'   => __( 'Yes. The Experience Builder supports Draft saving. Complete submissions can then be sent to the administrator for review.', 'torby' ),
    ),
    array(
        'question' => __( 'When will my Experience become public?', 'torby' ),
        'answer'   => __( 'It becomes public after the administrator approves and publishes it. Pending or Draft Experiences do not appear in the public marketplace.', 'torby' ),
    ),
    array(
        'question' => __( 'What is included with each booking?', 'torby' ),
        'answer'   => __( 'Every booking can include a premium e-bike, helmet, lock, and pickup/drop-off support at King Electric Bike Shop.', 'torby' ),
    ),
    array(
        'question' => __( 'How are bike quantities protected?', 'torby' ),
        'answer'   => __( 'Tourbi uses shared date, time, bike-type, and quantity inventory across normal rentals and Experiences, including temporary cart holds and checkout validation.', 'torby' ),
    ),
    array(
        'question' => __( 'Can I edit an approved Experience?', 'torby' ),
        'answer'   => __( 'Yes. Changes to an approved Experience are sent back for administrator review before the updated version is published.', 'torby' ),
    ),
);
?>
<section class="tourbi-host-section tourbi-host-faq">
    <div class="tourbi-shell--wide tourbi-host-faq__layout">
        <div class="tourbi-host-section__heading">
            <span class="tourbi-host-kicker">
                <?php esc_html_e( 'Questions Before You Start?', 'torby' ); ?>
            </span>

            <h2>
                <?php esc_html_e( 'Host frequently asked questions.', 'torby' ); ?>
            </h2>

            <p>
                <?php esc_html_e( 'Here are the essentials about pricing estimates, approval, included equipment, and the Host workflow.', 'torby' ); ?>
            </p>
        </div>

        <div
            class="tourbi-host-faq-list"
            data-tourbi-host-faq
        >
            <?php foreach ( $faqs as $index => $faq ) : ?>
                <article
                    class="tourbi-host-faq-item <?php echo 0 === $index ? 'is-open' : ''; ?>"
                >
                    <h3>
                        <button
                            type="button"
                            aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                            aria-controls="tourbi-host-faq-answer-<?php echo esc_attr( $index ); ?>"
                            data-tourbi-host-faq-toggle
                        >
                            <span><?php echo esc_html( $faq['question'] ); ?></span>
                            <b aria-hidden="true">+</b>
                        </button>
                    </h3>

                    <div
                        id="tourbi-host-faq-answer-<?php echo esc_attr( $index ); ?>"
                        class="tourbi-host-faq-item__answer"
                        <?php echo 0 === $index ? '' : 'hidden'; ?>
                    >
                        <p><?php echo esc_html( $faq['answer'] ); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
