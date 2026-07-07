(function () {
    'use strict';

    function clamp(value, minimum, maximum) {
        return Math.min(maximum, Math.max(minimum, value));
    }

    function numericValue(input) {
        if (!input) {
            return 0;
        }

        var parsed = Number(input.value);
        return Number.isFinite(parsed) ? parsed : Number(input.min || 0);
    }

    function formatPercent(value) {
        return Number(value).toLocaleString(undefined, {
            maximumFractionDigits: 2
        });
    }

    function setupCalculator(calculator) {
        if (calculator.dataset.tourbiReady === 'true') {
            return;
        }

        calculator.dataset.tourbiReady = 'true';

        var currency = calculator.dataset.currency || 'USD';
        var currencySymbol = calculator.dataset.currencySymbol || '$';
        var serviceFee = Number(calculator.dataset.platformFee || 5);
        var bikeRate = Number(calculator.dataset.bikeRate || 18);
        var formatter;

        try {
            formatter = new Intl.NumberFormat(document.documentElement.lang || undefined, {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } catch (error) {
            formatter = {
                format: function (value) {
                    return currencySymbol + Number(value).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            };
        }

        var ranges = {
            price: calculator.querySelector('[data-range="price"]'),
            guests: calculator.querySelector('[data-range="guests"]'),
            hours: calculator.querySelector('[data-range="hours"]')
        };

        var numbers = {
            price: calculator.querySelector('[data-number="price"]'),
            guests: calculator.querySelector('[data-number="guests"]'),
            hours: calculator.querySelector('[data-number="hours"]')
        };

        var payoutOutputs = calculator.querySelectorAll('[data-payout]');
        var grossOutput = calculator.querySelector('[data-gross]');
        var feeOutput = calculator.querySelector('[data-fee]');
        var bikeFeeOutput = calculator.querySelector('[data-bike-fee]');
        var formulaOutput = calculator.querySelector('[data-formula]');
        var resultLabel = calculator.querySelector('[data-result-label]');

        function updateRangeProgress(range) {
            if (!range) {
                return;
            }

            var minimum = Number(range.min || 0);
            var maximum = Number(range.max || 100);
            var value = numericValue(range);
            var progress = maximum === minimum ? 0 : ((value - minimum) / (maximum - minimum)) * 100;

            range.style.setProperty('--tourbi-range-progress', progress + '%');
            range.setAttribute('aria-valuenow', String(value));
        }

        function rangeKey(source, target) {
            return (target && target.dataset.range) || (source && source.dataset.number);
        }

        function setRangeFromNumber(source, target) {
            if (!source || !target) {
                return;
            }

            var rawValue = String(source.value).trim();

            if ('' === rawValue) {
                return;
            }

            var parsed = Number(rawValue);

            if (!Number.isFinite(parsed)) {
                return;
            }

            var minimum = Number(target.min || source.min || 0);
            var maximum = Number(target.max || source.max || 100);
            var value = clamp(parsed, minimum, maximum);
            var key = rangeKey(source, target);

            target.value = String(value);
            updateRangeProgress(ranges[key]);
            calculate();
        }

        function commitNumberValue(source, target) {
            if (!source || !target) {
                return;
            }

            var minimum = Number(target.min || source.min || 0);
            var maximum = Number(target.max || source.max || 100);
            var parsed = Number(source.value);
            var value = Number.isFinite(parsed)
                ? clamp(parsed, minimum, maximum)
                : Number(target.value || minimum);
            var key = rangeKey(source, target);

            source.value = String(value);
            target.value = String(value);
            updateRangeProgress(ranges[key]);
            calculate();
        }

        function calculate() {
            var price = numericValue(ranges.price);
            var guests = numericValue(ranges.guests);
            var hours = numericValue(ranges.hours);
            var gross = price * guests * hours;
            var bikeFee = bikeRate * guests * hours;
            var service = gross * (serviceFee / 100);
            var payout = Math.max(0, gross - bikeFee - service);

            payoutOutputs.forEach(function (output) {
                output.textContent = formatter.format(payout);
            });

            if (grossOutput) {
                grossOutput.textContent = formatter.format(gross);
            }

            if (bikeFeeOutput) {
                bikeFeeOutput.textContent = '-' + formatter.format(bikeFee);
            }

            if (feeOutput) {
                feeOutput.textContent = '-' + formatter.format(service);
            }

            if (resultLabel) {
                resultLabel.textContent = 'Estimated host earnings (' + hours + ' ' + (hours === 1 ? 'hour' : 'hours') + ')';
            }

            if (formulaOutput) {
                formulaOutput.textContent =
                    formatter.format(price) +
                    ' × ' + guests +
                    ' guests × ' + hours +
                    ' ' + (hours === 1 ? 'hour' : 'hours') +
                    ' − ' + formatter.format(bikeRate) +
                    ' × ' + guests +
                    ' bikes × ' + hours +
                    ' ' + (hours === 1 ? 'hour' : 'hours') +
                    ' − ' + formatPercent(serviceFee) +
                    '% service fee';
            }
        }

        Object.keys(ranges).forEach(function (key) {
            var range = ranges[key];
            var number = numbers[key];

            if (!range || !number) {
                return;
            }

            updateRangeProgress(range);

            range.addEventListener('input', function () {
                number.value = range.value;
                updateRangeProgress(range);
                calculate();
            });

            number.addEventListener('input', function () {
                setRangeFromNumber(number, range);
            });

            number.addEventListener('change', function () {
                commitNumberValue(number, range);
            });

            number.addEventListener('blur', function () {
                commitNumberValue(number, range);
            });
        });

        calculate();
    }

    function initialize() {
        document
            .querySelectorAll('.tourbi-income-calculator')
            .forEach(setupCalculator);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    document.addEventListener('elementor/popup/show', initialize);
}());
