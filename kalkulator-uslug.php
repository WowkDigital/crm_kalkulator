<?php
/*
Plugin Name: Kalkulator Usług
Description: Kalkulator oszczędności dla usług dostępnych w Crazy CRM.
Version: 1.0
Author: Twoje Imię
*/

if (!defined('ABSPATH')) {
    exit; // Zabezpieczenie przed bezpośrednim dostępem
}

class KalkulatorUslug
{
    public function __construct()
    {
        add_shortcode('kalkulator_uslug', array($this, 'render_kalkulator'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_get_services', array($this, 'get_services'));
        add_action('wp_ajax_nopriv_get_services', array($this, 'get_services'));
    }

    public function enqueue_scripts()
    {
        // Rejestracja i załadowanie stylów
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
        wp_enqueue_style('kalkulator-style', plugin_dir_url(__FILE__) . 'css/kalkulator-style.css');
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        // Rejestracja i załadowanie skryptów
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), null, true);
        wp_enqueue_script('confetti-js', 'https://cdn.jsdelivr.net/npm/confetti-js@0.0.18/dist/index.min.js', array(), null, true);
        wp_enqueue_script('kalkulator-script', plugin_dir_url(__FILE__) . 'js/kalkulator-script.js', array('jquery'), null, true);

        // Przekazanie danych do skryptu
        wp_localize_script('kalkulator-script', 'kalkulatorAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'services_nonce' => wp_create_nonce('services_nonce'),
            'plugin_url' => plugin_dir_url(__FILE__),
        ));
    }

    public function render_kalkulator()
    {
        ob_start();
        ?>
        <div class="container table-container">
            <h1 class="text-center mb-4">Sprawdź ile możesz oszczędzić z Crazy CRM!</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Wybrano</th>
                        <th scope="col">Usługa</th>
                        <th scope="col">Koszt</th>
                        <th scope="col">Ilość</th>
                        <th scope="col">Koszt usługi</th>
                        <th scope="col">Dostępne w CRM</th>
                    </tr>
                </thead>
                <tbody id="service-table-body">
                    <!-- Wiersze zostaną załadowane dynamicznie -->
                </tbody>
            </table>
            <button class="btn btn-primary calculate-btn mt-4" onclick="calculateTotal()">Oblicz</button>
            <div id="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Obliczamy ile oszczędzisz...</span>
                </div>
                <p>Obliczamy ile oszczędzisz...</p>
            </div>
            <div class="total text-center savings savings2">Oszczędzasz: <span id="savings">0</span> zł</div>
            <div class="total text-center mt-4">Cena po rabacie: <span id="total">0</span> zł</div>
            <div class="total text-center mt-4 full-price">Łączny koszt: <span id="original-total">0</span> zł</div>
        </div>
        <canvas id="confetti-canvas"></canvas>
        <?php
        return ob_get_clean();
    }

    public function get_services()
    {
        check_ajax_referer('services_nonce', 'nonce');

        $services = $this->load_services();

        wp_send_json_success($services);
    }

    private function load_services()
    {
        $services_csv = plugin_dir_path(__FILE__) . 'services.csv';

        if (!file_exists($services_csv)) {
            return array();
        }

        $data = array();
        if (($handle = fopen($services_csv, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) < 5) continue;

                // Pobieramy wartości z kolumn
                $service = $row[0];

                // Parsowanie kosztu (usunięcie tekstu i pozostawienie tylko liczby)
                $cost_str = $row[1];
                preg_match('/[\d\.]+/', $cost_str, $matches);
                $cost = isset($matches[0]) ? floatval($matches[0]) : 0;

                $variable = $row[2];

                // Parsowanie ceny (usunięcie tekstu i pozostawienie tylko liczby)
                $price_str = $row[3];
                preg_match('/[\d\.]+/', $price_str, $matches_price);
                $price = isset($matches_price[0]) ? floatval($matches_price[0]) : 0;

                $crm = $row[4];

                $data[] = array(
                    'service' => $service,
                    'cost' => $cost,
                    'variable' => $variable,
                    'price' => $price,
                    'crm' => $crm,
                );
            }
            fclose($handle);
        }
        return $data;
    }
}

new KalkulatorUslug();
