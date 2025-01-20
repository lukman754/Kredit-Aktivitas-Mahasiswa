from selenium import webdriver
from selenium.webdriver.common.by import By
import random

driver = webdriver.Chrome()  # Pastikan ChromeDriver terinstal
driver.get("https://my.unpam.ac.id/data-akademik/khs")  # Ganti dengan URL kuisioner

groups = driver.find_elements(By.CSS_SELECTOR, '[role="radiogroup"]')
for group in groups:
    radios = group.find_elements(By.CSS_SELECTOR, '[role="radio"]')
    filtered_radios = [radio for radio in radios if radio.get_attribute('aria-label') != "Sangat Tidak Setuju"]
    candidates = filtered_radios * 2 + [radio for radio in filtered_radios if radio.get_attribute('aria-label') == "Tidak Setuju"]
    random.choice(candidates).click()
