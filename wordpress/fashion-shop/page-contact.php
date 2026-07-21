<?php
/**
 * Template Name: Contact Page
 *
 * @package FashionShop
 */

get_header();

$phone = get_option('fashion_shop_phone', '+91 98765 43210');
$email = get_option('fashion_shop_email', 'info@fashionshop.com');
$address = get_option('fashion_shop_address', '123 Fashion Street, Karol Bagh, New Delhi - 110005');
?>

<section class="section page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-info-cards">
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4>Our Store</h4>
                    <p><?php echo nl2br(esc_html($address)); ?></p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-phone"></i>
                    <h4>Call Us</h4>
                    <p><?php echo esc_html($phone); ?></p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <h4>Email</h4>
                    <p><?php echo esc_html($email); ?></p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-clock"></i>
                    <h4>Working Hours</h4>
                    <p>Mon - Sat: 10AM - 8PM<br>Sunday: Closed</p>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <h3>Send us a Message</h3>
                <form class="contact-form" onsubmit="event.preventDefault(); alert('Thank you! Your message has been sent.'); this.reset();">
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="contactEmail">Email</label>
                            <input type="email" id="contactEmail" name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
