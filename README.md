# Kalkulator Usług

**Wersja:** 1.0  
**Autor:** Twoje Imię  

---

## Opis

Kalkulator Usług to wtyczka do WordPressa, która umożliwia użytkownikom obliczenie potencjalnych oszczędności przy korzystaniu z usług dostępnych w Crazy CRM. Wtyczka generuje interaktywny kalkulator na podstawie danych z pliku `services.csv`.

---

## Funkcjonalności

- Wyświetlanie listy usług z możliwością wyboru.  
- Automatyczne obliczanie kosztów na podstawie wybranych usług i ilości.  
- Prezentacja oszczędności po osiągnięciu określonego progu cenowego.  
- Efekty wizualne (konfetti) przy osiągnięciu oszczędności.  

---

## Instalacja

### Pobierz wtyczkę  
Skopiuj zawartość tego repozytorium lub pobierz pliki jako archiwum ZIP.

### Prześlij pliki do WordPressa  
Umieść folder `kalkulator-uslug` w katalogu `wp-content/plugins/` Twojej instalacji WordPressa.

### Aktywuj wtyczkę  
Zaloguj się do panelu administracyjnego WordPress, przejdź do sekcji **Wtyczki** i aktywuj wtyczkę **Kalkulator Usług**.

### Dodaj shortcode do strony  
Edytuj stronę lub wpis, na którym chcesz wyświetlić kalkulator, i dodaj shortcode:  

```csharp
[kalkulator_uslug]
