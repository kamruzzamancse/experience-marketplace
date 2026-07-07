<?php
/**
 * Template Name: Tourbi Contact Us
 * Template Post Type: page
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$contact_status  = '';
$contact_message = '';
$posted_values   = array(
    'name'    => '',
    'email'   => '',
    'topic'   => 'booking',
    'message' => '',
);

if ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['tourbi_contact_submit'] ) ) {
    $nonce = isset( $_POST['tourbi_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['tourbi_contact_nonce'] ) ) : '';

    $posted_values['name']    = isset( $_POST['tourbi_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['tourbi_contact_name'] ) ) : '';
    $posted_values['email']   = isset( $_POST['tourbi_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['tourbi_contact_email'] ) ) : '';
    $posted_values['topic']   = isset( $_POST['tourbi_contact_topic'] ) ? sanitize_text_field( wp_unslash( $_POST['tourbi_contact_topic'] ) ) : 'booking';
    $posted_values['message'] = isset( $_POST['tourbi_contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tourbi_contact_message'] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'tourbi_contact_form' ) ) {
        $contact_status  = 'error';
        $contact_message = __( 'We could not verify the form. Please refresh the page and try again.', 'torby' );
    } elseif ( '' === $posted_values['name'] || ! is_email( $posted_values['email'] ) || '' === $posted_values['message'] ) {
        $contact_status  = 'error';
        $contact_message = __( 'Please enter your name, a valid email address, and a message.', 'torby' );
    } else {
        $topic_labels = array(
            'booking' => __( 'Booking Question', 'torby' ),
            'hosting' => __( 'Host Application', 'torby' ),
            'support' => __( 'Support', 'torby' ),
            'other'   => __( 'Other', 'torby' ),
        );

        $topic_label = $topic_labels[ $posted_values['topic'] ] ?? $topic_labels['other'];
        $to          = get_option( 'admin_email' );
        $subject     = sprintf(
            /* translators: %s: contact topic. */
            __( 'Tourbi Contact: %s', 'torby' ),
            $topic_label
        );
        $body        = sprintf(
            "Name: %s\nEmail: %s\nTopic: %s\n\nMessage:\n%s\n",
            $posted_values['name'],
            $posted_values['email'],
            $topic_label,
            $posted_values['message']
        );
        $headers     = array(
            'Content-Type: text/plain; charset=UTF-8',
            'Reply-To: ' . $posted_values['name'] . ' <' . $posted_values['email'] . '>',
        );

        if ( wp_mail( $to, $subject, $body, $headers ) ) {
            $contact_status  = 'success';
            $contact_message = __( 'Thanks. Your message has been sent to the Tourbi team.', 'torby' );
            $posted_values   = array(
                'name'    => '',
                'email'   => '',
                'topic'   => 'booking',
                'message' => '',
            );
        } else {
            $contact_status  = 'error';
            $contact_message = __( 'Your message could not be sent right now. Please try again in a moment.', 'torby' );
        }
    }
}

get_header();
?>
<main id="primary" class="tourbi-contact-page-shell">
    <section class="tourbi-contact-hero">
        <div class="tourbi-contact-shell tourbi-contact-hero__grid">
            <div class="tourbi-contact-hero__copy">
                <span class="tourbi-contact-kicker"><?php esc_html_e( 'Contact Tourbi', 'torby' ); ?></span>
                <h1><?php esc_html_e( 'Questions about booking or hosting?', 'torby' ); ?></h1>
                <p>
                    <?php esc_html_e( 'Send a message and the Tourbi team will follow up with the next steps.', 'torby' ); ?>
                </p>
            </div>

            <div class="tourbi-contact-hero__panel" aria-label="Tourbi contact support">
                <span aria-hidden="true">↗</span>
                <strong><?php esc_html_e( 'Booking support', 'torby' ); ?></strong>
                <p><?php esc_html_e( 'Use the form below for booking questions, host applications, or launch support.', 'torby' ); ?></p>
            </div>
        </div>
    </section>

    <section class="tourbi-contact-content">
        <div class="tourbi-contact-shell tourbi-contact-layout">
            <div class="tourbi-contact-form-card">
                <span class="tourbi-contact-form-card__eyebrow"><?php esc_html_e( 'Send a message', 'torby' ); ?></span>
                <h2><?php esc_html_e( 'How can we help?', 'torby' ); ?></h2>

                <?php if ( $contact_message ) : ?>
                    <div class="tourbi-contact-alert tourbi-contact-alert--<?php echo esc_attr( $contact_status ); ?>" role="status">
                        <?php echo esc_html( $contact_message ); ?>
                    </div>
                <?php endif; ?>

                <form class="tourbi-contact-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>">
                    <?php wp_nonce_field( 'tourbi_contact_form', 'tourbi_contact_nonce' ); ?>

                    <div class="tourbi-contact-form__row">
                        <label for="tourbi-contact-name">
                            <?php esc_html_e( 'Name', 'torby' ); ?>
                            <span aria-hidden="true">*</span>
                        </label>
                        <input id="tourbi-contact-name" name="tourbi_contact_name" type="text" autocomplete="name" required value="<?php echo esc_attr( $posted_values['name'] ); ?>">
                    </div>

                    <div class="tourbi-contact-form__row">
                        <label for="tourbi-contact-email">
                            <?php esc_html_e( 'Email address', 'torby' ); ?>
                            <span aria-hidden="true">*</span>
                        </label>
                        <input id="tourbi-contact-email" name="tourbi_contact_email" type="email" autocomplete="email" required value="<?php echo esc_attr( $posted_values['email'] ); ?>">
                    </div>

                    <div class="tourbi-contact-form__row">
                        <label for="tourbi-contact-topic"><?php esc_html_e( 'I’m interested in', 'torby' ); ?></label>
                        <select id="tourbi-contact-topic" name="tourbi_contact_topic">
                            <option value="booking" <?php selected( $posted_values['topic'], 'booking' ); ?>><?php esc_html_e( 'Booking an experience', 'torby' ); ?></option>
                            <option value="hosting" <?php selected( $posted_values['topic'], 'hosting' ); ?>><?php esc_html_e( 'Becoming a host', 'torby' ); ?></option>
                            <option value="support" <?php selected( $posted_values['topic'], 'support' ); ?>><?php esc_html_e( 'Booking support', 'torby' ); ?></option>
                            <option value="other" <?php selected( $posted_values['topic'], 'other' ); ?>><?php esc_html_e( 'Other question', 'torby' ); ?></option>
                        </select>
                    </div>

                    <div class="tourbi-contact-form__row tourbi-contact-form__row--full">
                        <label for="tourbi-contact-message">
                            <?php esc_html_e( 'Message', 'torby' ); ?>
                            <span aria-hidden="true">*</span>
                        </label>
                        <textarea id="tourbi-contact-message" name="tourbi_contact_message" rows="6" required><?php echo esc_textarea( $posted_values['message'] ); ?></textarea>
                    </div>

                    <button type="submit" name="tourbi_contact_submit" value="1">
                        <?php esc_html_e( 'Send Message', 'torby' ); ?>
                        <span aria-hidden="true">→</span>
                    </button>
                </form>
            </div>

            <aside class="tourbi-contact-info" aria-label="Tourbi contact details">
                <article class="tourbi-contact-card tourbi-contact-card--primary">
                    <span class="tourbi-contact-card__icon" aria-hidden="true">✉</span>
                    <h2><?php esc_html_e( 'Email Tourbi', 'torby' ); ?></h2>
                    <p><?php esc_html_e( 'Messages from this form are routed to the Tourbi admin inbox for follow-up.', 'torby' ); ?></p>
                </article>

                <article class="tourbi-contact-card">
                    <span class="tourbi-contact-card__icon" aria-hidden="true">⌖</span>
                    <h2><?php esc_html_e( 'Visit / Pickup Location', 'torby' ); ?></h2>
                    <p><strong><?php esc_html_e( 'King Electric Bike Shop', 'torby' ); ?></strong></p>
                    <p><?php esc_html_e( '502 23rd Street. NW', 'torby' ); ?><br><?php esc_html_e( 'Washington, D.C. 20037', 'torby' ); ?></p>
                </article>

                <article class="tourbi-contact-card">
                    <span class="tourbi-contact-card__icon" aria-hidden="true">▣</span>
                    <h2><?php esc_html_e( 'Booking Support', 'torby' ); ?></h2>
                    <p><?php esc_html_e( 'Your booking request is received through the website. Tourbi will confirm booking details and coordinate any next steps.', 'torby' ); ?></p>
                </article>
            </aside>
        </div>
    </section>
</main>
<?php
get_footer();
