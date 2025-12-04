import math

def f(x):
    return math.exp(x) - 5 * x**2

def secant_table(x0, x1):
    epsilon1 = 0.00001
    epsilon2 = 0.000001
    Nmaks = 30
    berhenti = False
    i = 0

    print("------------------------------------------------")
    print(f"{'i':<5}{'x_r':<15}{'|x_(r+1) - x_r|':<15}")
    print("------------------------------------------------")

    print(f"{i:<5}{x0:<15.6f}{'-':<15}")
    i += 1
    diff = abs(x1 - x0)
    print(f"{i:<5}{x1:<15.6f}{diff:<15.6f}")

    while True:
        if abs(f(x1) - f(x0)) < epsilon2:
            berhenti = True
            break

        x2 = x1 - f(x1) * ((x1 - x0) / (f(x1) - f(x0)))
        diff = abs(x2 - x1)

        i += 1
        print(f"{i:<5}{x2:<15.6f}{diff:<15.6f}")

        if diff < epsilon1 or berhenti or i > Nmaks:
            break

        x0 = x1
        x1 = x2

    print("------------------------------------------------")
    if berhenti:
        print("Terjadi pembagian dengan bilangan yang hampir 0.")
    elif i > Nmaks:
        print("Proses divergen (melebihi batas iterasi maksimum).")
    else:
        print(f"Akar hampiran adalah x = {x2:.6f}")

# Jalankan
secant_table(0.5, 1)
