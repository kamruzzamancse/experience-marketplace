(function () {
    'use strict';

    function clamp(value, minimum, maximum) {
        return Math.min(maximum, Math.max(minimum, value));
    }

    function numericValue(input) {
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
        var platformFee = Number(calculator.dataset.platformFee || 15);
        var hostShare = Number(calculator.dataset.hostShare || (100 - platformFee));
        var period = 'monthly';

        var formatter;

        try {
            formatter = new Intl.NumberFormat(document.documentElement.lang || undefined, {
                style: 'currency',
                currency: currency,
                maximumFractionDigits: 0
            });
        } catch (error) {
            formatter = {
                format: function (value) {
                    return currencySymbol + Math.round(value).toLocaleString();
                }
            };
        }

        var ranges = {
            price: calculator.querySelector('[data-range="price"]'),
            guests: calculator.querySelector('[data-range="guests"]'),
            experiences: calculator.querySelector('[data-range="experiences"]')
        };

        var numbers = {
            price: calculator.querySelector('[data-number="price"]'),
            guests: calculator.querySelector('[data-number="guests"]'),
            experiences: calculator.querySelector('[data-number="experiences"]')
        };

        var payoutOutputs = calculator.querySelectorAll('[data-payout]');
        var grossOutput = calculator.querySelector('[data-gross]');
        var feeOutput = calculator.querySelector('[data-fee]');
        var formulaOutput = calculator.querySelector('[data-formula]');
        var resultLabel = calculator.querySelector('[data-result-label]');
        var periodButtons = calculator.querySelectorAll('[data-period]');

        function updateRangeProgress(range) {
            var minimum = Number(range.min || 0);
            var maximum = Number(range.max || 100);
            var value = numericValue(range);
            var progress = maximum === minimum
                ? 0
                : ((value - minimum) / (maximum - minimum)) * 100;

            range.style.setProperty('--tourbi-range-progress', progress + '%');
            range.setAttribute('aria-valuenow', String(value));
        }

        function synchronize(source, target) {
            var minimum = Number(target.min || source.min || 0);
            var maximum = Number(target.max || source.max || 100);
            var value = clamp(numericValue(source), minimum, maximum);

            source.value = String(value);
            target.value = String(value);
            updateRangeProgress(ranges[target.dataset.range || source.dataset.number]);
            calculate();
        }

        function calculate() {
            var price = numericValue(ranges.price);
            var guests = numericValue(ranges.guests);
            var experiences = numericValue(ranges.experiences);
            var multiplier = period === 'annual' ? 12 : 1;
            var grossMonthly = price * guests * experiences;
            var feeMonthly = grossMonthly * (platformFee / 100);
            var payoutMonthly = grossMonthly - feeMonthly;
            var gross = grossMonthly * multiplier;
            var fee = feeMonthly * multiplier;
            var payout = payoutMonthly * multiplier;

            payoutOutputs.forEach(function (output) {
                output.textContent = formatter.format(payout);
            });
            grossOutput.textContent = formatter.format(gross);
            feeOutput.textContent = '-' + formatter.format(fee);
            resultLabel.textContent = period === 'annual'
                ? 'Estimated annual host payout'
                : 'Estimated monthly host payout';

            formulaOutput.textContent =
                formatter.format(price) +
                ' × ' + guests +
                ' guests × ' + experiences +
                ' experiences' +
                (period === 'annual' ? ' × 12 months' : '') +
                ' × ' + formatPercent(hostShare) + '%';
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
                synchronize(number, range);
            });

            number.addEventListener('change', function () {
                synchronize(number, range);
            });
        });

        periodButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                period = button.dataset.period === 'annual'
                    ? 'annual'
                    : 'monthly';

                periodButtons.forEach(function (candidate) {
                    var isActive = candidate === button;
                    candidate.classList.toggle('is-active', isActive);
                    candidate.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                calculate();
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
})();
