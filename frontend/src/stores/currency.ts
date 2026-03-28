import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { currenciesService } from '@/api/services/currencies.service'
import type { Currency } from '@/api/types'

const STORAGE_KEY = 'selected_currency'

export const useCurrencyStore = defineStore('currency', () => {
  const currencies = ref<Currency[]>([])
  const selectedCode = ref<string>(localStorage.getItem(STORAGE_KEY) ?? 'MYR')

  const selected = computed<Currency | null>(
    () => currencies.value.find((c) => c.code === selectedCode.value) ?? null,
  )

  async function fetchCurrencies() {
    try {
      currencies.value = await currenciesService.getActive()
    } catch {
      // API unavailable — keep existing list (or stay empty)
    }
    // Fallback to MYR if the persisted currency is no longer active
    if (currencies.value.length > 0 && !selected.value) {
      selectCurrency('MYR')
    }
  }

  function selectCurrency(code: string) {
    selectedCode.value = code
    localStorage.setItem(STORAGE_KEY, code)
  }

  /**
   * Convert a MYR price to the selected currency.
   *
   * BNM rates: middle_rate = MYR per `unit` of foreign currency
   * → foreign = myr * unit / middle_rate
   */
  function convertFromMYR(myrPrice: number): number {
    const cur = selected.value
    if (!cur || cur.code === 'MYR' || !cur.middle_rate) return myrPrice
    return (myrPrice * cur.unit) / cur.middle_rate
  }

  /**
   * Format a MYR-denominated price in the selected currency.
   * Falls back to "SYM X.XX" if Intl doesn't recognise the code.
   */
  function formatPrice(myrPrice: number): string {
    const cur = selected.value
    if (!cur || cur.code === 'MYR') {
      return `RM ${myrPrice.toFixed(2)}`
    }

    const converted = convertFromMYR(myrPrice)

    try {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: cur.code,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(converted)
    } catch {
      const sym = cur.symbol ?? cur.code
      return `${sym} ${converted.toFixed(2)}`
    }
  }

  return { currencies, selectedCode, selected, fetchCurrencies, selectCurrency, convertFromMYR, formatPrice }
})
