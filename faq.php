<?php
/**
 * FAQ page for Genuine Landscapers website
 */

// Set page title and description
$page_title = "Frequently Asked Questions";
$page_description = "Find answers to common questions about our landscaping services, scheduling, pricing, and more.";

// Include header
include 'includes/header.php';
?>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <h1>Frequently Asked Questions</h1>
      <p>Answers to common questions about our services</p>
    </div>
  </section>

  <!-- FAQ Content -->
  <section class="faq-content">
    <div class="container">
      <div class="faq-intro animate-on-scroll">
        <h2>Common Questions About Our Services</h2>
        <p>We've compiled answers to the most frequently asked questions about our landscaping services. If you don't find the information you're looking for, please don't hesitate to <a href="contact.php">contact us</a> directly.</p>
      </div>
      
      <div class="faq-categories">
        <div class="faq-category animate-on-scroll" id="general-questions">
          <h2>General Questions</h2>
          <div class="faq-list">
            <div class="faq-item">
              <h3>What areas do you service?</h3>
              <div class="faq-answer">
                <p>We provide landscaping services throughout Windsor and surrounding areas, including LaSalle, Tecumseh, and Lakeshore. If you're unsure if we service your area, please contact us to confirm.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>Are you licensed and insured?</h3>
              <div class="faq-answer">
                <p>Yes, Genuine Landscapers is fully licensed and insured. We carry comprehensive general liability insurance and workers' compensation coverage for all our employees. We're happy to provide proof of insurance upon request.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>How long have you been in business?</h3>
              <div class="faq-answer">
                <p>Genuine Landscapers was established in 2015 and has been providing professional landscaping services to the Windsor area for over 10 years. Our team has extensive experience in all aspects of landscape maintenance.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>Do you offer free estimates?</h3>
              <div class="faq-answer">
                <p>Yes, we provide free estimates for all our services. You can request a quote through our website, by phone, or by email. For most residential properties, we can provide an estimate based on your property information. For larger or more complex properties, we may schedule an on-site evaluation.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="faq-category animate-on-scroll" id="services-questions">
          <h2>Services & Scheduling</h2>
          <div class="faq-list">
            <div class="faq-item">
              <h3>What services do you offer?</h3>
              <div class="faq-answer">
                <p>We specialize in landscape maintenance services including:</p>
                <ul>
                  <li>Lawn maintenance (mowing, edging, trimming)</li>
                  <li>Hedge and shrub trimming</li>
                  <li>Garden bed maintenance</li>
                  <li>Seasonal cleanup (spring and fall)</li>
                  <li>Sprinkler system installation and maintenance</li>
                  <li>And more!</li>
                </ul>
                <p>Visit our <a href="services.php">Services page</a> for more detailed information.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>How often will you service my property?</h3>
              <div class="faq-answer">
                <p>We offer flexible scheduling options based on your needs and the season. Most residential clients choose weekly service during the growing season (April-October) and reduced frequency during shoulder seasons. Commercial properties typically receive service 1-2 times per week. We'll work with you to create a schedule that keeps your property looking its best.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>Do I need to be home when you service my property?</h3>
              <div class="faq-answer">
                <p>No, you don't need to be home. Once we establish a service schedule, our team will arrive as planned whether you're home or not. Many of our clients provide gate codes or special access instructions if needed.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>How do you handle weather cancellations?</h3>
              <div class="faq-answer">
                <p>If service must be canceled due to inclement weather, we'll reschedule for the next available day. We monitor weather forecasts closely and will communicate any schedule changes in advance whenever possible.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="faq-category animate-on-scroll" id="pricing-questions">
          <h2>Pricing & Payment</h2>
          <div class="faq-list">
            <div class="faq-item">
              <h3>How do you determine pricing?</h3>
              <div class="faq-answer">
                <p>Our pricing is based on several factors including property size, service frequency, scope of work, and specific property features (slopes, obstacles, etc.). We provide detailed, transparent quotes so you know exactly what you're paying for.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>Do you offer any discounts?</h3>
              <div class="faq-answer">
                <p>Yes, we offer several discount opportunities:</p>
                <ul>
                  <li>10% senior discount for clients aged 65 and over</li>
                  <li>5% prepayment discount for seasonal contracts paid in full</li>
                  <li>Referral rewards program ($50 off for both you and the new client)</li>
                  <li>Multi-service discount when booking multiple services together</li>
                </ul>
                <p>Ask about our current promotions when requesting a quote.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>What payment methods do you accept?</h3>
              <div class="faq-answer">
                <p>We accept multiple payment methods for your convenience:</p>
                <ul>
                  <li>Credit/debit cards (Visa, MasterCard, American Express)</li>
                  <li>E-transfer</li>
                  <li>Checks</li>
                  <li>Cash</li>
                </ul>
                <p>We also offer automatic billing options for recurring services.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>Do you require contracts?</h3>
              <div class="faq-answer">
                <p>For regular maintenance services, we typically use a service agreement that outlines the scope of work, frequency, and pricing. These agreements can be month-to-month or seasonal, depending on your preference. For one-time services, a signed quote approval is sufficient.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="faq-category animate-on-scroll" id="special-questions">
          <h2>Special Circumstances</h2>
          <div class="faq-list">
            <div class="faq-item">
              <h3>Do you work with rental properties or property managers?</h3>
              <div class="faq-answer">
                <p>Yes, we work with many property managers and landlords to maintain rental properties. We can create customized maintenance programs for multiple properties and provide consolidated billing for easier management.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>Can you accommodate special requests?</h3>
              <div class="faq-answer">
                <p>Absolutely! We understand that every property and client has unique needs. Whether you have specific plant care instructions, pet considerations, or scheduling requirements, we're happy to accommodate special requests whenever possible.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>What if I'm not satisfied with the service?</h3>
              <div class="faq-answer">
                <p>Your satisfaction is our priority. If you're ever unhappy with any aspect of our service, please contact us immediately. We'll return to address any issues at no additional cost. We stand behind our work and are committed to ensuring your complete satisfaction.</p>
              </div>
            </div>
            
            <div class="faq-item">
              <h3>Do you offer snow removal services in winter?</h3>
              <div class="faq-answer">
                <p>Currently, we focus exclusively on landscaping maintenance services and do not offer snow removal. However, we can recommend trusted partners who provide quality snow removal services during the winter months.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="cta-panel animate-on-scroll">
        <h2>Still have questions?</h2>
        <p>We're here to help! Contact us directly for answers to any questions not covered here.</p>
        <div class="cta-buttons">
          <a href="contact.php" class="btn btn-primary">Contact Us</a>
          <a href="index.php#quote" class="btn btn-secondary">Request a Quote</a>
        </div>
      </div>
    </div>
  </section>

<?php
// Include footer
include 'includes/footer.php';
?>
