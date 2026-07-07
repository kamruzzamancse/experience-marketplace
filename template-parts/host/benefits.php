<?php
/**
 * Host benefits section.
 *
 * @package Torby
 */
?>
<section class="tourbi-host-section tourbi-host-benefits">
    <div class="tourbi-shell--wide">
        <div class="tourbi-host-section__heading">
            <span class="tourbi-host-kicker">
                <?php esc_html_e( 'Simple Pricing', 'torby' ); ?>
            </span>

            <h2>
                <?php esc_html_e( 'Transparent pricing for every hosted ride.', 'torby' ); ?>
            </h2>

            <p>
                <?php esc_html_e( 'Tourbi keeps hosting simple: you set the guest price, we provide the e-bikes, and the pricing stays easy to understand.', 'torby' ); ?>
            </p>
        </div>

        <div class="tourbi-host-benefit-grid tourbi-host-benefit-grid--pricing">
            <article>
                <span aria-hidden="true">$18</span>
                <h3><?php esc_html_e( 'Per bike per hour', 'torby' ); ?></h3>
                <p><?php esc_html_e( 'Each Tourbi e-bike is calculated at $18 per bike for each hour of the hosted experience.', 'torby' ); ?></p>
            </article>

            <article>
                <span aria-hidden="true">5%</span>
                <h3><?php esc_html_e( 'Service fee', 'torby' ); ?></h3>
                <p><?php esc_html_e( 'A 5% service fee helps cover marketplace operations, support, and booking tools.', 'torby' ); ?></p>
            </article>

            <article>
                <span aria-hidden="true">◎</span>
                <h3><?php esc_html_e( 'You set the ticket price', 'torby' ); ?></h3>
                <p><?php esc_html_e( 'Hosts can price their experience based on the value, guest count, and route they create.', 'torby' ); ?></p>
            </article>

            <article>
                <span aria-hidden="true">▣</span>
                <h3><?php esc_html_e( 'Booking tools included', 'torby' ); ?></h3>
                <p><?php esc_html_e( 'Guests can select dates, availability, quantities, and checkout through the booking flow.', 'torby' ); ?></p>
            </article>
        </div>
    </div>
</section>
